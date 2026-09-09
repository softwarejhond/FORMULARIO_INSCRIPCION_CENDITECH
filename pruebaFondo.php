<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fondo interactivo</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        .kinetic-grid {
            position: relative;
            width: 100%;
            min-height: 100vh;
            overflow: hidden;
            background: #ffffff;
        }

        .kinetic-grid canvas {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 0;
            pointer-events: none;
        }

        .kinetic-grid__content {
            position: relative;
            z-index: 10;
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding-left: 1.5rem;
            padding-right: 1.5rem;
            text-align: center;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }

        .kinetic-grid__badge {
            margin-bottom: 1.25rem;
            border: 1px solid rgba(25, 58, 112, 0.25);
            border-radius: 9999px;
            padding: 0.25rem 0.75rem;
            font-size: 0.75rem;
            font-weight: 500;
            letter-spacing: 0.025em;
            color: rgba(25, 58, 112, 0.8);
        }

        .kinetic-grid__title {
            max-width: 42rem;
            font-size: 2.25rem;
            font-weight: 600;
            letter-spacing: -0.025em;
            color: #193A70;
        }

        .kinetic-grid__text {
            margin-top: 1rem;
            max-width: 28rem;
            font-size: 1rem;
            color: rgba(25, 58, 112, 0.55);
        }

        @media (min-width: 640px) {
            .kinetic-grid__title {
                font-size: 3.75rem;
            }
        }
    </style>
</head>

<body>
    <div class="kinetic-grid">
        <canvas id="kinetic-grid-canvas"></canvas>

        <div class="kinetic-grid__content">
            <span class="kinetic-grid__badge">Interactive Background</span>
            <h1 class="kinetic-grid__title">Move your cursor. Click anywhere.</h1>
            <p class="kinetic-grid__text">
                A kinetic grid that warps toward the pointer and ripples on every
                click.
            </p>
        </div>
    </div>

    <script>
        (function() {
            var canvas = document.getElementById('kinetic-grid-canvas');
            if (!canvas) return;
            var ctx = canvas.getContext('2d');
            if (!ctx) return;

            // ─── Constants ─────────────────────────────────────────────────────────
            var CELL_SIZE = 55;
            var INFLUENCE_RADIUS = 260;
            var MAX_WARP = 24;
            var DOT_SPACING = 28;
            var LERP_SPEED = 0.08;

            var LINE_BASE = {
                r: 25,
                g: 58,
                b: 112,
                a: 0.12
            };
            var NODE_BASE_RADIUS = 1.8;
            var NODE_ACTIVE_RADIUS = 3.2;

            var theme = {
                bg: '#ffffff',
                lineActive: {
                    r: 25,
                    g: 58,
                    b: 112,
                    a: 0.9
                },
                nodeActive: {
                    r: 25,
                    g: 58,
                    b: 112,
                    a: 1.0
                },
                glow: '25,58,112',
                ripple: '25,58,112'
            };

            // ─── State ─────────────────────────────────────────────────────────────
            var size = {
                w: 0,
                h: 0
            };
            var mouse = {
                x: -9999,
                y: -9999
            };
            var targetMouse = {
                x: -9999,
                y: -9999
            };
            var ripples = [];
            var raf = 0;

            // ─── Helpers ───────────────────────────────────────────────────────────
            function lerpN(a, b, t) {
                return a + (b - a) * t;
            }

            function lerpColor(base, active, t) {
                var r = Math.round(lerpN(base.r, active.r, t));
                var g = Math.round(lerpN(base.g, active.g, t));
                var b = Math.round(lerpN(base.b, active.b, t));
                var a = lerpN(base.a, active.a, t);
                return 'rgba(' + r + ',' + g + ',' + b + ',' + a.toFixed(3) + ')';
            }

            function getWarpedPoint(gx, gy, col, row, m, rip, cols, rows) {
                var edgeMargin = 1.5;
                var colPin = Math.min(col / edgeMargin, (cols - 1 - col) / edgeMargin, 1);
                var rowPin = Math.min(row / edgeMargin, (rows - 1 - row) / edgeMargin, 1);
                var pinFactor = colPin * colPin * rowPin * rowPin;

                var dx = gx - m.x;
                var dy = gy - m.y;
                var dist = Math.sqrt(dx * dx + dy * dy);

                var proximity = Math.max(0, 1 - dist / INFLUENCE_RADIUS) * pinFactor;

                var rx = 0,
                    ry = 0;
                for (var i = 0; i < rip.length; i++) {
                    var r = rip[i];
                    var rdx = gx - r.x;
                    var rdy = gy - r.y;
                    var rdist = Math.sqrt(rdx * rdx + rdy * rdy);
                    var waveWidth = 55;
                    var diff = rdist - r.radius;
                    if (Math.abs(diff) < waveWidth) {
                        var strength = (1 - Math.abs(diff) / waveWidth) * r.opacity * 18 * pinFactor;
                        var angle = Math.atan2(rdy, rdx);
                        var sign = diff < 0 ? -1 : 1;
                        rx += Math.cos(angle) * strength * sign * -1;
                        ry += Math.sin(angle) * strength * sign * -1;
                    }
                }

                if (dist < INFLUENCE_RADIUS && dist > 0 && pinFactor > 0) {
                    var t = dist / INFLUENCE_RADIUS;
                    var eased = t < 0.01 ? 0 : (1 - t) * (1 - t) * Math.min(1, dist / 60);
                    var warpAmt = eased * MAX_WARP * pinFactor;
                    var warpAngle = Math.atan2(dy, dx);
                    return {
                        pt: {
                            x: gx - Math.cos(warpAngle) * warpAmt + rx,
                            y: gy - Math.sin(warpAngle) * warpAmt + ry
                        },
                        proximity: proximity
                    };
                }

                return {
                    pt: {
                        x: gx + rx,
                        y: gy + ry
                    },
                    proximity: proximity
                };
            }

            // ─── Draw ──────────────────────────────────────────────────────────────
            function draw(now) {
                var W = size.w;
                var H = size.h;

                ctx.clearRect(0, 0, W, H);

                ctx.fillStyle = theme.bg;
                ctx.fillRect(0, 0, W, H);

                // Static background dot texture
                ctx.fillStyle = 'rgba(25,58,112,0.06)';
                for (var x = DOT_SPACING / 2; x < W; x += DOT_SPACING) {
                    for (var y = DOT_SPACING / 2; y < H; y += DOT_SPACING) {
                        ctx.beginPath();
                        ctx.arc(x, y, 0.7, 0, Math.PI * 2);
                        ctx.fill();
                    }
                }

                // Update ripples
                for (var i = ripples.length - 1; i >= 0; i--) {
                    var r = ripples[i];
                    var age = (now - r.born) / 1000;
                    r.radius = Math.max(0, age * 400);
                    r.opacity = Math.max(0, 1 - age * 1.2);
                    if (r.opacity <= 0) ripples.splice(i, 1);
                }

                // Build warped grid
                var cols = Math.max(2, Math.ceil(W / CELL_SIZE)) + 1;
                var rows = Math.max(2, Math.ceil(H / CELL_SIZE)) + 1;
                var cellW = W / (cols - 1);
                var cellH = H / (rows - 1);

                var pts = [];
                var prox = [];

                for (var row = 0; row < rows; row++) {
                    pts[row] = [];
                    prox[row] = [];
                    for (var col = 0; col < cols; col++) {
                        var res = getWarpedPoint(
                            col * cellW, row * cellH, col, row,
                            mouse, ripples, cols, rows
                        );
                        pts[row][col] = res.pt;
                        prox[row][col] = res.proximity;
                    }
                }

                // Grid lines
                function drawSeg(p1, p2, pr1, pr2) {
                    var avg = (pr1 + pr2) / 2;
                    var t = avg * avg * (3 - 2 * avg);
                    ctx.beginPath();
                    ctx.moveTo(p1.x, p1.y);
                    ctx.lineTo(p2.x, p2.y);
                    ctx.strokeStyle = lerpColor(LINE_BASE, theme.lineActive, t);
                    ctx.lineWidth = lerpN(0.8, 1.5, t);
                    ctx.stroke();
                }

                ctx.lineCap = 'butt';

                for (var r2 = 0; r2 < rows; r2++) {
                    for (var c2 = 0; c2 < cols - 1; c2++) {
                        drawSeg(pts[r2][c2], pts[r2][c2 + 1], prox[r2][c2], prox[r2][c2 + 1]);
                    }
                }

                for (var c3 = 0; c3 < cols; c3++) {
                    for (var r3 = 0; r3 < rows - 1; r3++) {
                        drawSeg(pts[r3][c3], pts[r3 + 1][c3], prox[r3][c3], prox[r3 + 1][c3]);
                    }
                }

                // Intersection nodes
                for (var r4 = 0; r4 < rows; r4++) {
                    for (var c4 = 0; c4 < cols; c4++) {
                        var p = pts[r4][c4];
                        var pr = prox[r4][c4];
                        var tNode = pr * pr * (3 - 2 * pr);
                        var radius = lerpN(NODE_BASE_RADIUS, NODE_ACTIVE_RADIUS, tNode);

                        if (tNode > 0.3) {
                            var glowR = radius + lerpN(0, 6, (tNode - 0.3) / 0.7);
                            var grd = ctx.createRadialGradient(p.x, p.y, radius * 0.5, p.x, p.y, glowR);
                            grd.addColorStop(0, 'rgba(' + theme.glow + ',' + (tNode * 0.3).toFixed(3) + ')');
                            grd.addColorStop(1, 'rgba(' + theme.glow + ',0)');
                            ctx.beginPath();
                            ctx.arc(p.x, p.y, glowR, 0, Math.PI * 2);
                            ctx.fillStyle = grd;
                            ctx.fill();
                        }

                        ctx.beginPath();
                        ctx.arc(p.x, p.y, radius, 0, Math.PI * 2);
                        ctx.fillStyle = lerpColor({
                                r: 25,
                                g: 58,
                                b: 112,
                                a: 0.18
                            },
                            theme.nodeActive,
                            tNode
                        );
                        ctx.fill();
                    }
                }

                // Ripple rings
                for (var j = 0; j < ripples.length; j++) {
                    var rip = ripples[j];
                    var safeRadius = Math.max(0, rip.radius);
                    ctx.beginPath();
                    ctx.arc(rip.x, rip.y, safeRadius, 0, Math.PI * 2);
                    ctx.strokeStyle = 'rgba(' + theme.ripple + ',' + (rip.opacity * 0.28).toFixed(3) + ')';
                    ctx.lineWidth = 1.5;
                    ctx.stroke();
                }
            }

            // ─── Animation loop ────────────────────────────────────────────────────
            function animate(now) {
                mouse.x = lerpN(mouse.x, targetMouse.x, LERP_SPEED);
                mouse.y = lerpN(mouse.y, targetMouse.y, LERP_SPEED);

                draw(now);
                raf = requestAnimationFrame(animate);
            }

            // ─── Setup ─────────────────────────────────────────────────────────────
            function setSize() {
                var w = window.innerWidth;
                var h = window.innerHeight;
                canvas.width = w;
                canvas.height = h;
                size.w = w;
                size.h = h;
                if (mouse.x === -9999) {
                    mouse = {
                        x: -9999,
                        y: -9999
                    };
                    targetMouse = {
                        x: -9999,
                        y: -9999
                    };
                }
            }

            setSize();
            window.addEventListener('resize', setSize);

            window.addEventListener('mousemove', function(e) {
                targetMouse = {
                    x: e.clientX,
                    y: e.clientY
                };
            });

            window.addEventListener('click', function(e) {
                ripples.push({
                    x: e.clientX,
                    y: e.clientY,
                    radius: 0,
                    opacity: 1,
                    born: performance.now()
                });
            });

            raf = requestAnimationFrame(animate);
        })();
    </script>
</body>

</html>