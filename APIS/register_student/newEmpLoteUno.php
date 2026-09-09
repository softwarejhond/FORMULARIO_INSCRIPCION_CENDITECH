<style>
    .form-admin-container {
        max-width: auto;
        margin: 0 auto;
        background: #f8f9fa;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.16);
        padding: 32px 24px;
    }

    .form-admin-container label {
        font-weight: 500;
    }

    .text-danger {
        color: #dc3545;
    }

    .form-check-input {
        border: 2px solid #0d6efd !important;
        background-color: #e7f1ff !important;
        width: 1.3em;
        height: 1.3em;
        margin-right: 8px;
    }

    .form-check-input:checked {
        background-color: #0d6efd !important;
        border-color: #0d6efd !important;
    }

    .btn-success {
        background-color: #28a745;
        border-color: #28a745;
    }

    .btn-success:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }
</style>

<div class="form-admin-container mt-4 mb-4">
    <form id="formLoteUno" method="POST" autocomplete="off" novalidate>
        <!-- Campo hidden para lote -->
        <input type="hidden" name="lote" id="lote" value="1">

        <div class="form-section" id="section-1">

            <div class="col-12 mb-3">
                <h4 class="mb-3">Datos de identificación básica</h4>
            </div>
            <!-- SECCION 1 - DATOS PERSONALES -->
            <div class="row">
                <div class="col-md-12">
                    <label for="interest" class="form-label">¿En que bootcamp esta inscrito/a? <span class="text-danger">*</span></label>
                    <select name="interest" id="interest" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="Programación">Programación</option>
                        <option value="Análisis de datos">Análisis de datos</option>
                        <option value="Inteligencia artificial">Inteligencia artificial</option>
                        <option value="Blockchain">Blockchain</option>
                        <option value="Computración en la nube">Computración en la nube</option>
                    </select>
                </div>
            </div><br>

            <div class="row mb-3">

                <div class="col-md-6">
                    <label for="start_training_date" class="form-label">
                        Fecha en la que iniciará su formación con Talento Tech 2025 <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="start_training_date" id="start_training_date" class="form-control" required>
                </div>

                <div class="col-md-6">
                    <label for="personal_description" class="form-label">Descripción personal (opcional)</label>
                    <textarea name="personal_description" id="personal_description" class="form-control" rows="1" maxlength="255" placeholder="Cuéntanos brevemente sobre ti..."></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="typeID" class="form-label">Tipo de identificación <span class="text-danger">*</span></label>
                    <select name="typeID" id="typeID" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="CC">Cédula de ciudadanía</option>
                        <option value="TI">Tarjeta de identidad</option>
                        <option value="PPT">Permiso por Protección Temporal (PPT)</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="number_id" class="form-label">Número de identificación <span class="text-danger">*</span></label>
                    <input type="text" name="number_id" id="number_id" class="form-control" pattern="\d{5,15}" maxlength="15" required oninput="this.value=this.value.replace(/\D/g,'')">
                </div>
                <div class="col-md-4">
                    <label for="number_id_very" class="form-label">Verifique número de identificación <span class="text-danger">*</span></label>
                    <input type="text" name="number_id_very" id="number_id_very" class="form-control" pattern="\d{5,15}" maxlength="15" required oninput="this.value=this.value.replace(/\D/g,'')">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="email" class="form-label">Correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="email" id="email" class="form-control" maxlength="100" required>
                    <small id="emailError" class="text-danger" style="display:none;">Formato de correo no válido.</small>
                </div>
                <div class="col-md-6">
                    <label for="email_very" class="form-label">Verifique correo electrónico <span class="text-danger">*</span></label>
                    <input type="email" name="email_very" id="email_very" class="form-control" maxlength="100" required>
                    <small id="emailVeryError" class="text-danger" style="display:none;">Formato de correo no válido.</small>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="first_name" class="form-label">Primer nombre <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" id="first_name" class="form-control" maxlength="50" required>
                </div>
                <div class="col-md-6">
                    <label for="second_name" class="form-label">Segundo nombre</label>
                    <input type="text" name="second_name" id="second_name" class="form-control" maxlength="50">
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="first_last" class="form-label">Primer apellido <span class="text-danger">*</span></label>
                    <input type="text" name="first_last" id="first_last" class="form-control" maxlength="50" required>
                </div>
                <div class="col-md-6">
                    <label for="second_last" class="form-label">Segundo apellido <span class="text-danger">*</span></label>
                    <input type="text" name="second_last" id="second_last" class="form-control" maxlength="50" required>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="localidad" class="form-label">Localidad en la que vive <span class="text-danger">*</span></label>
                    <select name="localidad" id="localidad" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="Usaquén">Usaquén</option>
                        <option value="Chapinero">Chapinero</option>
                        <option value="Santa Fe">Santa Fe</option>
                        <option value="San Cristóbal">San Cristóbal</option>
                        <option value="Usme">Usme</option>
                        <option value="Tunjuelito">Tunjuelito</option>
                        <option value="Bosa">Bosa</option>
                        <option value="Kennedy">Kennedy</option>
                        <option value="Fontibón">Fontibón</option>
                        <option value="Engativá">Engativá</option>
                        <option value="Suba">Suba</option>
                        <option value="Barrios Unidos">Barrios Unidos</option>
                        <option value="Teusaquillo">Teusaquillo</option>
                        <option value="Los Mártires">Los Mártires</option>
                        <option value="Antonio Nariño">Antonio Nariño</option>
                        <option value="Puente Aranda">Puente Aranda</option>
                        <option value="La Candelaria">La Candelaria</option>
                        <option value="Rafael Uribe Uribe">Rafael Uribe Uribe</option>
                        <option value="Ciudad Bolívar">Ciudad Bolívar</option>
                        <option value="Sumapaz">Sumapaz</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="nivel_educativo" class="form-label">Nivel educativo <span class="text-danger">*</span></label>
                    <select name="nivel_educativo" id="nivel_educativo" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="Básica completa">Básica completa</option>
                        <option value="Básica incompleta">Básica incompleta</option>
                        <option value="Media incompleta">Media incompleta</option>
                        <option value="Media completa">Media completa</option>
                        <option value="Técnico profesional incompleta">Técnico profesional incompleta</option>
                        <option value="Técnico profesional completa">Técnico profesional completa</option>
                        <option value="Universitaria incompleta">Universitaria incompleta</option>
                        <option value="Universitaria completa">Universitaria completa</option>
                        <option value="Postgrado">Postgrado</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="gender" class="form-label">Género <span class="text-danger">*</span></label>
                    <select name="gender" id="gender" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="Hombre">Hombre</option>
                        <option value="Mujer">Mujer</option>
                        <option value="LGBTIQ+">LGBTIQ+</option>
                        <option value="No binario">No binario</option>
                        <option value="No reporta">No reporta</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-section" id="section-2" style="display:none;">
            <!-- SECCION 2 - DATOS LABORALES -->
            <div class="row mb-3">
                <div class="col-md-12">
                    <label for="work_experience" class="form-label">Experiencia laboral (opcional)</label>
                    <textarea name="work_experience" id="work_experience" class="form-control" rows="2" maxlength="255" placeholder="Describe brevemente tu experiencia laboral, si tienes."></textarea>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="current_employment_status" class="form-label">Situación laboral actual <span class="text-danger">*</span></label>
                    <select name="current_employment_status" id="current_employment_status" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="Empleado">Empleado</option>
                        <option value="Desempleado">Desempleado</option>
                        <option value="Estudiando">Estudiando</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="tech_experience" class="form-label">¿Cuenta con experiencia laboral en el sector de Tecnología? <span class="text-danger">*</span></label>
                    <select name="tech_experience" id="tech_experience" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="Si">Sí</option>
                        <option value="No">No</option>
                    </select>
                </div>

                <div class="row mb-3 mt-4">
                    <div class="col-md-12">
                        <label for="job_profile" class="form-label">Perfil laboral (opcional)</label>
                        <textarea name="job_profile" id="job_profile" class="form-control" rows="2" maxlength="255" placeholder="Describe brevemente tu perfil laboral, si lo deseas."></textarea>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="tech_experience_years" class="form-label">
                        Años de experiencia tiene en el sector Tech <span class="text-danger">*</span>
                    </label>
                    <input type="number" name="tech_experience_years" id="tech_experience_years" class="form-control" min="0" max="50" step="1" required placeholder="Ejemplo: 3">
                </div>
                <div class="col-md-8">
                    <label for="last_tech_role" class="form-label">
                        Especifique el último rol o perfil desempeñado en el sector de la Tecnología
                    </label>
                    <select name="last_tech_role" id="last_tech_role" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="Desarrollador de Software">Desarrollador de Software (Frontend, Backend, Full Stack, Mobile, Ingeniero de Software)</option>
                        <option value="Estructura y Soporte">Estructura y Soporte (Ingeniero de Sistemas, Soporte Técnico, Administrador de Redes y Sistemas, Especialista en DevOps)</option>
                        <option value="Ciencia de Datos y Análisis">Ciencia de Datos y Análisis (Analista de Datos, Científico de Datos, Ingeniero de Datos)</option>
                        <option value="Ciberseguridad">Ciberseguridad (Analista de Seguridad Informática, Especialista en Seguridad de la Información)</option>
                        <option value="Diseño y Experiencia de Usuario">Diseño y Experiencia de Usuario (Diseñador UX/UI, Diseñador Web)</option>
                        <option value="Gestión de proyectos y Producto">Gestión de proyectos y Producto (Scrum Master, Product Owner, Project Manager TI)</option>
                        <option value="Tecnologías Emergentes y Especializadas">Tecnologías Emergentes y Especializadas (Inteligencia Artificial, Blockchain, IoT)</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="skills_knowledge" class="form-label">Habilidades y conocimientos (opcional)</label>
                    <textarea name="skills_knowledge" id="skills_knowledge" class="form-control" rows="2" maxlength="255" placeholder="Describe tus habilidades y conocimientos..."></textarea>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">
                        <h5>Competencias digitales certificadas (Puede seleccionar más de 1)<span class="text-danger">*</span></h5>
                    </label>
                    <div class="row mt-2 mb-4">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill1" value="Programación de software">
                                <label class="form-check-label" for="skill1">Programación de software</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill2" value="Soporte de sistemas informáticos">
                                <label class="form-check-label" for="skill2">Soporte de sistemas informáticos</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill3" value="Redes de datos">
                                <label class="form-check-label" for="skill3">Redes de datos</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill4" value="Análisis y Desarrollo de Sistemas de Información">
                                <label class="form-check-label" for="skill4">Análisis y Desarrollo de Sistemas de Información</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill5" value="Ingeniería Electrónica / Telecomunicaciones">
                                <label class="form-check-label" for="skill5">Ingeniería Electrónica / Telecomunicaciones</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill6" value="Programación">
                                <label class="form-check-label" for="skill6">Programación</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill7" value="Ciberseguridad">
                                <label class="form-check-label" for="skill7">Ciberseguridad</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill8" value="Excel avanzado para análisis de datos">
                                <label class="form-check-label" for="skill8">Excel avanzado para análisis de datos</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill9" value="Diseño UX/UI">
                                <label class="form-check-label" for="skill9">Diseño UX/UI</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill10" value="Desarrollo Web Full Stack">
                                <label class="form-check-label" for="skill10">Desarrollo Web Full Stack</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill11" value="Análisis de Datos">
                                <label class="form-check-label" for="skill11">Análisis de Datos</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill12" value="Testing QA">
                                <label class="form-check-label" for="skill12">Testing QA</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill13" value="Machine Learning">
                                <label class="form-check-label" for="skill13">Machine Learning</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill14" value="Cisco (CCNA, CyberOps Associate)">
                                <label class="form-check-label" for="skill14">Cisco (CCNA, CyberOps Associate)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill15" value="Microsoft: Azure Fundamentals, Power BI Data Analyst">
                                <label class="form-check-label" for="skill15">Microsoft: Azure Fundamentals, Power BI Data Analyst</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill16" value="AWS: Cloud Practitioner, Solutions Architect Associate">
                                <label class="form-check-label" for="skill16">AWS: Cloud Practitioner, Solutions Architect Associate</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill17" value="Scrum: Scrum Master, Product Owner">
                                <label class="form-check-label" for="skill17">Scrum: Scrum Master, Product Owner</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill18" value="CompTIA: Security+, Network+">
                                <label class="form-check-label" for="skill18">CompTIA: Security+, Network+</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill19" value="Google: Certificaciones en soporte TI, análisis de datos, UX">
                                <label class="form-check-label" for="skill19">Google: Certificaciones en soporte TI, análisis de datos, UX</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill20" value="Meta, IBM, Coursera (Programas certificados)">
                                <label class="form-check-label" for="skill20">Meta, IBM, Coursera (Programas certificados)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill21" value="Mantenimiento de computadores">
                                <label class="form-check-label" for="skill21">Mantenimiento de computadores</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="digital_skills[]" id="skill22" value="Otro">
                                <label class="form-check-label" for="skill22">Otro</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-section" id="section-3" style="display:none;">
            <!-- SECCION 3 - DATOS LABORALES TECH -->
            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">
                        <h5>Especifique las habilidades blandas que reconoce tiene en la actualidad <span class="text-danger">*</span></h5>
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="soft_skills[]" id="soft_skill1" value="Habilidades de comunicación">
                        <label class="form-check-label" for="soft_skill1">
                            Habilidades de comunicación (Comunicación asertiva, escucha activa, redacción clara y técnica)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="soft_skills[]" id="soft_skill2" value="Habilidades de trabajo en equipo y colaboración">
                        <label class="form-check-label" for="soft_skill2">
                            Habilidades de trabajo en equipo y colaboración (Trabajo colaborativo, empatía)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="soft_skills[]" id="soft_skill3" value="Habilidades para la resolución de conflictos">
                        <label class="form-check-label" for="soft_skill3">
                            Habilidades para la resolución de conflictos (Solución creativa de problemas, identificación de casusas raíz)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="soft_skills[]" id="soft_skill4" value="Habilidades de gestión personal y profesional">
                        <label class="form-check-label" for="soft_skill4">
                            Habilidades de gestión personal y profesional (Gestión del tiempo, autonomía, compromiso)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="soft_skills[]" id="soft_skill5" value="Habilidades de liderazgo y proactividad">
                        <label class="form-check-label" for="soft_skill5">
                            Habilidades de liderazgo y proactividad -incluso sin estar en un rol formal de líder- (Iniciativa y tomas de decisiones, visión estratégica)
                        </label>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">
                        <h5>Especifique las redes sociales profesionales en las que se encuentra inscrito/a <span class="text-danger">*</span></h5>
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="professional_networks[]" id="network_linkedin" value="LinkedIn">
                        <label class="form-check-label" for="network_linkedin">LinkedIn</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="professional_networks[]" id="network_platforms" value="Plataformas digitales">
                        <label class="form-check-label" for="network_platforms">Plataformas digitales</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="professional_networks[]" id="network_researchgate" value="ResearchGate">
                        <label class="form-check-label" for="network_researchgate">ResearchGate</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="professional_networks[]" id="network_github" value="GitHub">
                        <label class="form-check-label" for="network_github">GitHub</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="professional_networks[]" id="network_other" value="Otro">
                        <label class="form-check-label" for="network_other">Otro</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="professional_networks[]" id="network_none" value="No usa redes sociales profesionales">
                        <label class="form-check-label" for="network_none">No usa redes sociales profesionales</label>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">
                        <h5>Especifique el rol al que le gustaría vincularse laboralmente. <span class="text-danger">*</span></h5>
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="desired_role" id="desired_role1" value="Desarrollador de Software" required>
                        <label class="form-check-label" for="desired_role1">
                            Desarrollador de Software (Frontend, Backend, Full Stack, Mobile, Ingeniero de Software)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="desired_role" id="desired_role2" value="Estructura y Soporte">
                        <label class="form-check-label" for="desired_role2">
                            Estructura y Soporte (Ingeniero de Sistemas, Soporte Técnico, Administrador de Redes y Sistemas, Especialista en DevOps)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="desired_role" id="desired_role3" value="Ciencia de Datos y Análisis">
                        <label class="form-check-label" for="desired_role3">
                            Ciencia de Datos y Análisis (Analista de Datos, Científico de Datos, Ingeniero de Datos)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="desired_role" id="desired_role4" value="Ciberseguridad">
                        <label class="form-check-label" for="desired_role4">
                            Ciberseguridad (Analista de Seguridad Informática, Especialista en Seguridad de la Información)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="desired_role" id="desired_role5" value="Diseño y Experiencia de Usuario">
                        <label class="form-check-label" for="desired_role5">
                            Diseño y Experiencia de Usuario (Diseñador UX/UI, Diseñador Web)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="desired_role" id="desired_role6" value="Gestión de proyectos y Producto">
                        <label class="form-check-label" for="desired_role6">
                            Gestión de proyectos y Producto (Scrum Master, Product Owner, Project Manager TI)
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="desired_role" id="desired_role7" value="Tecnologías Emergentes y Especializadas">
                        <label class="form-check-label" for="desired_role7">
                            Tecnologías Emergentes y Especializadas (Inteligencia Artificial, Blockchain, IoT)
                        </label>
                    </div>
                </div>
            </div><br><br>

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input me-2" type="checkbox" id="accept_requirements" name="accept_requirements" required>
                        <label class="form-check-label" for="accept_requirements">
                            Acepta los requisitos establecidos por la presente convocatoria <br>
                            <a href="https://talentotech.utinnova.co/politica-de-tratamiento-de-datos/" target="_blank" rel="noopener" class="ms-2">Puedes consultar los requisitos de la convocatoria haciendo click aquí</a>
                            <span class="text-danger">*</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="form-check d-flex align-items-center">
                        <input class="form-check-input me-2" type="checkbox" id="accept_data_policies" name="accept_data_policies" required>
                        <label class="form-check-label" for="accept_data_policies">
                            Confirmo que he leído y acepto las políticas de tratamiento de datos personales <br>
                            <a href="https://drive.google.com/file/d/1r6acAm9TflaQBxfBm8WvX8QiC1REO0Qv/view" target="_blank" rel="noopener" class="ms-2">Puedes consultar las políticas haciendo click aquí</a>
                            <span class="text-danger">*</span>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="button" id="prevBtn" class="btn btn-secondary me-2">Anterior</button>
            <button type="button" id="nextBtn" class="btn btn-primary">Siguiente</button>
            <button type="submit" id="saveBtn" class="btn btn-success btn-lg px-5" style="display:none;">
                <i class="bi bi-save me-2"></i>
                Guardar
            </button>
        </div>
    </form>
</div>

<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Validación de formato de correo en tiempo real
    document.getElementById('email').addEventListener('input', function() {
        const email = this.value;
        const emailError = document.getElementById('emailError');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) && email.length > 0) {
            emailError.style.display = 'block';
        } else {
            emailError.style.display = 'none';
        }
    });

    document.getElementById('email_very').addEventListener('input', function() {
        const email = this.value;
        const emailError = document.getElementById('emailVeryError');
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) && email.length > 0) {
            emailError.style.display = 'block';
        } else {
            emailError.style.display = 'none';
        }
    });

    // Validación en tiempo real para coincidencia de número de documento
    document.getElementById('number_id_very').addEventListener('input', function() {
        const number_id = document.getElementById('number_id').value;
        const number_id_very = this.value;
        let errorMsg = document.getElementById('numberIdVeryError');
        if (!errorMsg) {
            errorMsg = document.createElement('small');
            errorMsg.id = 'numberIdVeryError';
            errorMsg.className = 'text-danger';
            this.parentNode.appendChild(errorMsg);
        }
        if (number_id !== number_id_very && number_id_very.length > 0) {
            errorMsg.textContent = 'El número de identificación no coincide.';
            errorMsg.style.display = 'block';
        } else {
            errorMsg.textContent = '';
            errorMsg.style.display = 'none';
        }
    });

    // Validación en tiempo real para coincidencia de email
    document.getElementById('email_very').addEventListener('input', function() {
        const email = document.getElementById('email').value;
        const email_very = this.value;
        let errorMsg = document.getElementById('emailVeryMatchError');
        if (!errorMsg) {
            errorMsg = document.createElement('small');
            errorMsg.id = 'emailVeryMatchError';
            errorMsg.className = 'text-danger';
            this.parentNode.appendChild(errorMsg);
        }
        if (email !== email_very && email_very.length > 0) {
            errorMsg.textContent = 'El correo electrónico no coincide.';
            errorMsg.style.display = 'block';
        } else {
            errorMsg.textContent = '';
            errorMsg.style.display = 'none';
        }
    });

    document.getElementById('tech_experience').addEventListener('change', function() {
        const yearsInput = document.getElementById('tech_experience_years');
        const lastRoleSelect = document.getElementById('last_tech_role');
        if (this.value === 'No') {
            yearsInput.value = 0;
            yearsInput.setAttribute('readonly', 'readonly');
            yearsInput.setAttribute('disabled', 'disabled');
            // Buscar o crear la opción "No aplica"
            let noAplicaOption = lastRoleSelect.querySelector('option[value="No aplica"]');
            if (!noAplicaOption) {
                noAplicaOption = document.createElement('option');
                noAplicaOption.value = "No aplica";
                noAplicaOption.textContent = "No aplica";
                lastRoleSelect.appendChild(noAplicaOption);
            }
            lastRoleSelect.value = "No aplica";
            lastRoleSelect.setAttribute('readonly', 'readonly');
            lastRoleSelect.removeAttribute('disabled'); // <-- Importante: quitar disabled para que se envíe
        } else {
            yearsInput.removeAttribute('readonly');
            yearsInput.removeAttribute('disabled');
            // Si estaba la opción "No aplica", la quitamos
            let noAplicaOption = lastRoleSelect.querySelector('option[value="No aplica"]');
            if (noAplicaOption) {
                noAplicaOption.remove();
            }
            lastRoleSelect.value = "";
            lastRoleSelect.removeAttribute('readonly');
            lastRoleSelect.removeAttribute('disabled');
        }
    });

    const sections = document.querySelectorAll('.form-section');
    let currentSection = 0;

    function showSection(index) {
        sections.forEach((sec, i) => {
            sec.style.display = i === index ? 'block' : 'none';
        });
        document.getElementById('prevBtn').style.display = index === 0 ? 'none' : 'inline-block';
        document.getElementById('nextBtn').style.display = index === sections.length - 1 ? 'none' : 'inline-block';
        document.getElementById('saveBtn').style.display = index === sections.length - 1 ? 'inline-block' : 'none';
    }

    document.getElementById('nextBtn').addEventListener('click', function() {
        if (currentSection < sections.length - 1) {
            currentSection++;
            showSection(currentSection);
        }
    });

    document.getElementById('prevBtn').addEventListener('click', function() {
        if (currentSection > 0) {
            currentSection--;
            showSection(currentSection);
        }
    });

    // Inicializa
    showSection(currentSection);

    // MEJORADO: Manejo del envío del formulario con mejor debugging
    document.getElementById('formLoteUno').addEventListener('submit', function(e) {
        e.preventDefault();

        // Validaciones básicas antes del envío
        const email = document.getElementById('email').value;
        const emailVery = document.getElementById('email_very').value;
        const numberId = document.getElementById('number_id').value;
        const numberIdVery = document.getElementById('number_id_very').value;

        // Verificar que los emails coincidan
        if (email !== emailVery) {
            Swal.fire('Error', 'Los correos electrónicos no coinciden', 'error');
            return;
        }

        // Verificar que los números de ID coincidan
        if (numberId !== numberIdVery) {
            Swal.fire('Error', 'Los números de identificación no coinciden', 'error');
            return;
        }

        // Validar que al menos un checkbox esté marcado en cada grupo requerido
        const digitalSkills = document.querySelectorAll('input[name="digital_skills[]"]:checked');
        const softSkills = document.querySelectorAll('input[name="soft_skills[]"]:checked');
        const professionalNetworks = document.querySelectorAll('input[name="professional_networks[]"]:checked');

        if (digitalSkills.length === 0) {
            Swal.fire('Error', 'Debe seleccionar al menos una competencia digital', 'error');
            return;
        }

        if (softSkills.length === 0) {
            Swal.fire('Error', 'Debe seleccionar al menos una habilidad blanda', 'error');
            return;
        }

        if (professionalNetworks.length === 0) {
            Swal.fire('Error', 'Debe seleccionar al menos una red social profesional', 'error');
            return;
        }

        // Crear FormData
        const formData = new FormData(this);

        // Mostrar loading
        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor espere mientras se procesa su información',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Realizar la petición
        fetch('APIS/register_student/saveEmployability.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);

                // Primero verificar si la respuesta es correcta
                if (!response.ok) {
                    throw new Error(`HTTP ${response.status}: ${response.statusText}`);
                }

                // Intentar obtener el texto de la respuesta para debugging
                return response.text();
            })
            .then(text => {
                console.log('Response text:', text);

                // Intentar parsear como JSON
                try {
                    const data = JSON.parse(text);

                    if (data.success) {
                        Swal.fire({
                            title: '¡Éxito!',
                            text: data.message || 'Registro guardado correctamente.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            this.reset();
                            currentSection = 0;
                            showSection(currentSection);
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Ocurrió un error al guardar.', 'error');
                    }
                } catch (jsonError) {
                    console.error('JSON Parse Error:', jsonError);
                    console.error('Response was:', text);
                    Swal.fire({
                        title: 'Error de respuesta',
                        html: `El servidor devolvió una respuesta inválida.<br><small>Respuesta: ${text.substring(0, 200)}...</small>`,
                        icon: 'error'
                    });
                }
            })
            .catch(error => {
                console.error('Network/Server Error:', error);
                Swal.fire({
                    title: 'Error de conexión',
                    html: `No se pudo conectar con el servidor.<br><small>Error: ${error.message}</small>`,
                    icon: 'error'
                });
            });
    });
</script>