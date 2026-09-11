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
    <form id="formCierre" method="POST" autocomplete="off" novalidate>

        <div class="form-section" id="section-1">

            <div class="col-12 mb-3">
                <h4 class="mb-3">Datos de identificación básica</h4>
            </div>
            <!-- SECCION 1 - DATOS PERSONALES -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="interest" class="form-label">¿En que bootcamp esta inscrito/a? <span class="text-danger">*</span></label>
                    <select name="interest" id="interest" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="Programación">Programación</option>
                        <option value="Análisis de datos">Análisis de datos</option>
                        <option value="Inteligencia artificial">Inteligencia artificial</option>
                        <option value="Blockchain">Blockchain</option>
                        <option value="Computación en la nube">Computación en la nube</option>
                        <option value="Ciberseguridad">Ciberseguridad</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="start_training_date" class="form-label">
                        Fecha en la que terminó su formación con Talento Tech 2025 <span class="text-danger">*</span>
                    </label>
                    <input type="date" name="start_training_date" id="start_training_date" class="form-control" required>
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
                    <label for="grupos_poblacionales" class="form-label">Grupo poblacional <span class="text-danger">*</span></label>
                    <select name="grupos_poblacionales" id="grupos_poblacionales" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="Condición de discapacidad">Condición de discapacidad</option>
                        <option value="Victima del conflicto armado">Victima del conflicto armado</option>
                        <option value="Indigena">Indigena</option>
                        <option value="Afrodescendiente">Afrodescendiente</option>
                        <option value="Refugiado o Migrante">Refugiado o Migrante</option>
                        <option value="Madre/padre Soltero/a">Madre/padre Soltero/a</option>
                        <option value="Perteneciente a la comunidad LGTBIQ+">Perteneciente a la comunidad LGTBIQ+</option>
                        <option value="Sisben A/B/C">Sisben A/B/C</option>
                        <option value="No aplica">Ninguno de los anteriores</option>
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
                    <label for="current_tech_job" class="form-label">¿Actualmente trabajas en tecnología o el sector TI? <span class="text-danger">*</span></label>
                    <select name="current_tech_job" id="current_tech_job" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="Si">Sí</option>
                        <option value="No">No</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="employment_obtained_by" class="form-label">
                        ¿Cómo consiguió su empleo actual? <span class="text-danger">*</span>
                    </label>
                    <select name="employment_obtained_by" id="employment_obtained_by" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="Redes Sociales Profesionales">Redes Sociales Profesionales</option>
                        <option value="Participación en ferias de Empleo Talento TECH">Participación en ferias de Empleo Talento TECH</option>
                        <option value="Otro">Otro</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="contract_type" class="form-label">
                        Tipo de contrato bajo en el que se encuentra vinculado laboralmente <span class="text-danger">*</span>
                    </label>
                    <select name="contract_type" id="contract_type" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="Término Indefinido">Término Indefinido</option>
                        <option value="Término Fijo">Término Fijo</option>
                        <option value="Obra o Labor">Obra o Labor</option>
                        <option value="Temporal">Temporal</option>
                        <option value="Contrato de Aprendizaje">Contrato de Aprendizaje</option>
                        <option value="Prestación de Servicios">Prestación de Servicios</option>
                        <option value="No aplica">No aplica</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label for="income_level" class="form-label">Especifique su nivel de ingresos <span class="text-danger">*</span></label>
                    <select name="income_level" id="income_level" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="Menos de 1 SMMLV">Menos de 1 SMMLV</option>
                        <option value="1 SMMLV">1 SMMLV</option>
                        <option value="2 SMMLV">2 SMMLV</option>
                        <option value="3 SMMLV">3 SMMLV</option>
                        <option value="4 SMMLV">4 SMMLV</option>
                        <option value="Más de 4 SMMLV">Más de 4 SMMLV</option>
                        <option value="No aplica">No aplica</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">
                        <h5>Especifique el rol o perfil que desempeña en su trabajo <span class="text-danger">*</span></h5>
                    </label>
                    <div class="row mt-2 mb-4">
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="current_job_role" id="job_role1" value="Desarrollador de Software" required>
                                <label class="form-check-label" for="job_role1">
                                    Desarrollador de Software (Frontend, Backend, Full Stack, Mobile, Ingeniero de Software)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="current_job_role" id="job_role2" value="Estructura y Soporte">
                                <label class="form-check-label" for="job_role2">
                                    Estructura y Soporte (Ingeniero de Sistemas, Soporte Técnico, Administrador de Redes y Sistemas, Especialista en DevOps)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="current_job_role" id="job_role3" value="Ciencia de Datos y Análisis">
                                <label class="form-check-label" for="job_role3">
                                    Ciencia de Datos y Análisis (Analista de Datos, Científico de Datos, Ingeniero de Datos)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="current_job_role" id="job_role4" value="Ciberseguridad">
                                <label class="form-check-label" for="job_role4">
                                    Ciberseguridad (Analista de Seguridad Informática, Especialista en Seguridad de la Información)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="current_job_role" id="job_role5" value="Diseño y Experiencia de Usuario">
                                <label class="form-check-label" for="job_role5">
                                    Diseño y Experiencia de Usuario (Diseñador UX/UI, Diseñador Web)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="current_job_role" id="job_role6" value="Gestión de proyectos y Producto">
                                <label class="form-check-label" for="job_role6">
                                    Gestión de proyectos y Producto (Scrum Master, Product Owner, Project Manager TI)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="current_job_role" id="job_role7" value="Tecnologías Emergentes y Especializadas">
                                <label class="form-check-label" for="job_role7">
                                    Tecnologías Emergentes y Especializadas (Inteligencia Artificial, Blockchain, IoT)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="current_job_role" id="job_role8" value="Otro">
                                <label class="form-check-label" for="job_role8">
                                    Otro
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="current_job_role" id="job_role9" value="No aplica">
                                <label class="form-check-label" for="job_role9">
                                    No aplica
                                </label>
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
                        <h5>Especifique en qué espacios de la Ruta de Empleo participó <span class="text-danger">*</span></h5>
                    </label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="employment_route_spaces[]" id="employment_fairs" value="Ferias de Empleo">
                        <label class="form-check-label" for="employment_fairs">
                            Ferias de Empleo
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="employment_route_spaces[]" id="networking_spaces" value="Espacios de Networking">
                        <label class="form-check-label" for="networking_spaces">
                            Espacios de Networking
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="employment_route_spaces[]" id="mentorships" value="Mentorías">
                        <label class="form-check-label" for="mentorships">
                            Mentorías
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="employment_route_spaces[]" id="selection_processes" value="Procesos de selección/Prácticas">
                        <label class="form-check-label" for="selection_processes">
                            Procesos de selección/Prácticas
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="employment_route_spaces[]" id="employment_skills_training" value="Formación habilidades para el empleo">
                        <label class="form-check-label" for="employment_skills_training">
                            Formación habilidades para el empleo (Preparación de Hojas de Vida y Entrevista)
                        </label>
                    </div>
                </div>
            </div><br>

            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">
                        <h5>¿En qué medida consideras que el contenido socializado sobre hoja de vida, entrevista, networking es útil para aplicar y mejorar su currículum? <span class="text-danger">*</span></h5>
                    </label>
                    <div class="d-flex align-items-center mb-2">
                        <span class="me-2 text-nowrap">Muy poco útil</span>
                        <div class="d-flex justify-content-between w-100">
                            <div class="d-flex flex-column align-items-center flex-fill">
                                <label for="usefulness1">1</label>
                                <input class="form-check-input" type="radio" name="content_usefulness" id="usefulness1" value="1" required>
                            </div>
                            <div class="d-flex flex-column align-items-center flex-fill">
                                <label for="usefulness2">2</label>
                                <input class="form-check-input" type="radio" name="content_usefulness" id="usefulness2" value="2">
                            </div>
                            <div class="d-flex flex-column align-items-center flex-fill">
                                <label for="usefulness3">3</label>
                                <input class="form-check-input" type="radio" name="content_usefulness" id="usefulness3" value="3">
                            </div>
                            <div class="d-flex flex-column align-items-center flex-fill">
                                <label for="usefulness4">4</label>
                                <input class="form-check-input" type="radio" name="content_usefulness" id="usefulness4" value="4">
                            </div>
                            <div class="d-flex flex-column align-items-center flex-fill">
                                <label for="usefulness5">5</label>
                                <input class="form-check-input" type="radio" name="content_usefulness" id="usefulness5" value="5">
                            </div>
                        </div>
                        <span class="ms-2 text-nowrap">Muy útil</span>
                    </div>

                </div>
            </div><br>

            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">
                        <h5>¿En qué medida considera que los contenidos compartidos desde la ruta de empleo le apoyaron a aumentar su red de contactos y le facilitaron el acceso a plataformas de empleo? <span class="text-danger">*</span></h5>
                    </label>
                    <div class="d-flex align-items-center mb-2">
                        <span class="me-2 text-nowrap">Poco apoyo</span>
                        <div class="d-flex justify-content-between w-100">
                            <div class="d-flex flex-column align-items-center flex-fill">
                                <label for="support1">1</label>
                                <input class="form-check-input" type="radio" name="employment_support" id="support1" value="1" required>
                            </div>
                            <div class="d-flex flex-column align-items-center flex-fill">
                                <label for="support2">2</label>
                                <input class="form-check-input" type="radio" name="employment_support" id="support2" value="2">
                            </div>
                            <div class="d-flex flex-column align-items-center flex-fill">
                                <label for="support3">3</label>
                                <input class="form-check-input" type="radio" name="employment_support" id="support3" value="3">
                            </div>
                            <div class="d-flex flex-column align-items-center flex-fill">
                                <label for="support4">4</label>
                                <input class="form-check-input" type="radio" name="employment_support" id="support4" value="4">
                            </div>
                            <div class="d-flex flex-column align-items-center flex-fill">
                                <label for="support5">5</label>
                                <input class="form-check-input" type="radio" name="employment_support" id="support5" value="5">
                            </div>
                        </div>
                        <span class="ms-2 text-nowrap">Bastante apoyo</span>
                    </div>
                </div>
            </div><br>



            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">
                        <h5>Especifique su nivel de satisfacción general, frente a las actividades de empleo en las que participó. <span class="text-danger">*</span></h5>
                    </label>
                    <div class="mb-2">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="general_satisfaction" id="satisfaction_low" value="Poco satisfecho" required>
                            <label class="form-check-label" for="satisfaction_low">
                                Poco satisfecho
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="general_satisfaction" id="satisfaction_high" value="Muy satisfecho">
                            <label class="form-check-label" for="satisfaction_high">
                                Muy satisfecho
                            </label>
                        </div>
                    </div>
                </div>
            </div><br>
            
            <div class="row mb-3">
                <div class="col-12">
                    <label class="form-label">
                        <h5>¿Cuál de las siguientes acciones considera más relevante para mejorar la Ruta de Empleabilidad? <span class="text-danger">*</span></h5>
                    </label>
                    <select name="improvement_action" id="improvement_action" class="form-control" required>
                        <option value="">Seleccione...</option>
                        <option value="Mejorar los canales de información y difusión sobre oportunidades laborales">Mejorar los canales de información y difusión sobre oportunidades laborales</option>
                        <option value="Integrar mentorías o coaching individualizado para el desarrollo profesional">Integrar mentorías o coaching individualizado para el desarrollo profesional</option>
                        <option value="Brindar apoyo en elaboración de hojas de vida y portafolios profesionales">Brindar apoyo en elaboración de hojas de vida y portafolios profesionales</option>
                        <option value="Incorporar simulaciones de entrevistas y procesos de selección reales">Incorporar simulaciones de entrevistas y procesos de selección reales</option>
                        <option value="Incluir formación en emprendimiento y autoempleo como alternativa laboral">Incluir formación en emprendimiento y autoempleo como alternativa laboral</option>
                        <option value="Realizar campañas de sensibilización para empresas sobre la empleabilidad de los egresados del programa">Realizar campañas de sensibilización para empresas sobre la empleabilidad de los egresados del programa</option>
                        <option value="Ninguna de las anteriores">Ninguna de las anteriores</option>
                        <option value="Otra">Otra…</option>
                    </select>
                </div>
            </div>
            <br><br>

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
    document.getElementById('formCierre').addEventListener('submit', function(e) {
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
        fetch('APIS/register_student/saveEmployabilityClose.php', {
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