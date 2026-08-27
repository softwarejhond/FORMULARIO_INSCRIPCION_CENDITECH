<?php
return [
    'include_fields' => [
        'typeID',
        'number_id',
        'number_id_very',
        'first_name',
        'second_name',
        'first_last',
        'second_last',
        'birthdate',
        'expedition_date',
        'gender',
        'marital_status',
        'email',
        'first_phone',
        'second_phone',
        'emergency_contact_name',
        'emergency_contact_number',
        'nationality',
        'department',
        'municipality',
        'address',
        'people_charge',
        'vulnerable_population',
        'vulnerable_type',
        'ethnic_group',
        'stratum',
        'residence_area',
        'country_person',
        'training_level',
        'occupation',
        'time_obligations',
        'motivations_belong_program',
        'current_situation',
        'impediment_complete_course',
        'availability',
        'mode',
        'headquarters',
        'program',
        'prior_knowledge',
        'level',
        'languages',
        'languages_level',
        'medical_condition',
        'disability',
        'type_disability',
        'pregnancy',
        'technologies',
        'internet',
        'knowledge_program',
        'accept_requirements',
        'accepts_tech_talent',
        'accept_data_policies',
        // 'file_front_id',
        // 'file_back_id',
        // 'has_certification',
        // 'program_certified',
    ],
    'typeID' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">Tipo de D.I <span class="required">*</span></label>',
        'options' => [
            'CC' => 'C.C',

        ],
        'attributes' => [
            'class' => 'form-control mb-3 form-select',
            'required' => true,
            'name' => 'typeID'
        ]
    ],
    'number_id' => [
        'type' => 'text',
        'label' => '<label class="form-label mb-2">Número de D.I <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'number_id',
            'id' => 'number_id',
            'inputmode' => 'numeric',
            'oninput' => "this.value=this.value.replace(/[^0-9]/g, '')"
        ]
    ],
    'number_id_very' => [
        'type' => 'text',
        'label' => '<label class="form-label mb-2">Confirmación # de D.I <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'number_id_very',
            'id' => 'number_id_very',
            'inputmode' => 'numeric',
            'oninput' => "this.value=this.value.replace(/[^0-9]/g, '')"
        ]
    ],
    'first_name' => [
        'type' => 'text',
        'label' => '<label class="form-label mb-2">Primer nombre <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'first_name'
        ]
    ],
    'second_name' => [
        'type' => 'text',
        'label' => '<label class="form-label mb-2">Segundo nombre</label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'name' => 'second_name'
        ]
    ],
    'first_last' => [
        'type' => 'text',
        'label' => '<label class="form-label mb-2">Primer apellido <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'first_last'
        ]
    ],
    'second_last' => [
        'type' => 'text',
        'label' => '<label class="form-label mb-2">Segundo apellido</label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'name' => 'second_last'
        ]
    ],
    'birthdate' => [
        'type' => 'custom',
        'label' => '<label class="form-label mt-3">Fecha de nacimiento <span class="required">*</span></label>',
        'fields' => [
            'day' => [
                'type' => 'select',
                'attributes' => [
                    'class' => 'form-control mt-3 form-select birthdate-field',
                    'required' => true,
                    'name' => 'birthdate_day',
                    'id' => 'birthdate_day',
                ],
                'options' => array_combine(range(1, 31), range(1, 31)), // Días del 1 al 31
            ],
            'month' => [
                'type' => 'select',
                'attributes' => [
                    'class' => 'form-control mt-3 form-select birthdate-field',
                    'required' => true,
                    'name' => 'birthdate_month',
                    'id' => 'birthdate_month',
                ],
                'options' => [
                    1 => 'Enero',
                    2 => 'Febrero',
                    3 => 'Marzo',
                    4 => 'Abril',
                    5 => 'Mayo',
                    6 => 'Junio',
                    7 => 'Julio',
                    8 => 'Agosto',
                    9 => 'Septiembre',
                    10 => 'Octubre',
                    11 => 'Noviembre',
                    12 => 'Diciembre'
                ],
            ],
            'year' => [
                'type' => 'select',
                'attributes' => [
                    'class' => 'form-control mb-3 form-select birthdate-field',
                    'required' => true,
                    'name' => 'birthdate_year',
                    'id' => 'birthdate_year',
                ],
                'options' => array_combine(range(date('Y') - 100, date('Y')), range(date('Y') - 100, date('Y'))), // Últimos 100 años
            ],
        ],
    ],


    'expedition_date' => [
        'type' => 'custom',
        'label' => '<label class="form-label mt-3">Fecha de expedición del ID <span class="required">*</span></label>',
        'fields' => [
            'day' => [
                'type' => 'select',
                'attributes' => [
                    'class' => 'form-control mb-3 form-select',
                    'required' => true,
                    'name' => 'expedition_day',
                ],
                'options' => array_combine(range(1, 31), range(1, 31)), // Días del 1 al 31
            ],
            'month' => [
                'type' => 'select',
                'attributes' => [
                    'class' => 'form-control mb-3 form-select',
                    'required' => true,
                    'name' => 'expedition_month',
                ],
                'options' => [
                    1 => 'Enero',
                    2 => 'Febrero',
                    3 => 'Marzo',
                    4 => 'Abril',
                    5 => 'Mayo',
                    6 => 'Junio',
                    7 => 'Julio',
                    8 => 'Agosto',
                    9 => 'Septiembre',
                    10 => 'Octubre',
                    11 => 'Noviembre',
                    12 => 'Diciembre'
                ], // Meses
            ],
            'year' => [
                'type' => 'select',
                'attributes' => [
                    'class' => 'form-control mb-3 form-select',
                    'required' => true,
                    'name' => 'expedition_year',
                ],
                'options' => array_combine(range(date('Y') - 100, date('Y')), range(date('Y') - 100, date('Y'))), // Últimos 100 años
            ],
        ],
    ],

    'gender' => [
        'type' => 'select',
        'label' => '<label class="form-label mt-3">Género <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',
            'Hombre' => 'Hombre',
            'Mujer' => 'Mujer',
            'Intersexual' => 'Intersexual',
            'No binario' => 'No binario',
            'LGTBIQ+' => 'LGBTIQ+',
            'Otro' => 'Otro',
            'No reporta' => 'No reporta'
        ],
        'attributes' => [
            'class' => 'form-control mb-3 form-select',
            'required' => true,
            'name' => 'gender'
        ]
    ],

    'marital_status' => [
        'type' => 'select',
        'label' => '<label class="form-label mt-3">Estado civil <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',
            'Soltero/a' => 'Soltero/a',
            'Casado/a' => 'Casado/a',
            'Divorciado/a' => 'Divorciado/a',
            'Viudo/a' => 'Viudo/a',
            'Separado/a' => 'Separado/a',
            'Unión libre' => 'Unión libre'
        ],
        'attributes' => [
            'class' => 'form-control mb-3 form-select',
            'required' => true,
            'name' => 'marital_status'
        ]
    ],
    'email' => [
        'type' => 'email',
        'label' => '<label class="form-label mb-2">Correo electrónico <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'email'
        ]
    ],
    'first_phone' => [
        'type' => 'number',
        'label' => '<label class="form-label mt-3">Celular <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'first_phone',
            'min' => '1000000000', // Número mínimo (10 dígitos)
            'max' => '9999999999', // Número máximo (10 dígitos)
            'maxlength' => '10', // Limita la longitud a 10 caracteres (aunque 'maxlength' no aplica bien para 'number')
            'title' => 'El número debe contener exactamente 10 dígitos', // Mensaje de error personalizado
            'oninput' => 'validatePhoneInput(this)', // Llama a la función JavaScript para validar la entrada
            'onkeypress' => 'restrictInput(event)', // Llama a la función para restringir la entrada de caracteres
        ],
    ],





    'second_phone' => [
        'type' => 'tel',
        'label' => '<label class="form-label mt-3">Segunda opción de contacto <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'second_phone'
        ]
    ],
    'country_select' => [
        'type' => 'select',
        'label' => '<label class="form-label mt-2">Seleccionar país <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'country_code'
        ]
    ],

    'emergency_contact_name' => [
        'type' => 'text',
        'label' => '<label class="form-label mt-3">Nombre del contacto de emergencia <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'emergency_contact_name'
        ]
    ],
    'emergency_contact_number' => [
        'type' => 'tel',
        'label' => '<label class="form-label mb-2">Número del contacto de emergencia <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'emergency_contact_number'
        ]
    ],
    'nationality' => [
        'type' => 'select',
        'label' => '<label class="form-label mt-3">Nacionalidad <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',
            'Colombiana' => 'Colombiana'
        ],
        'attributes' => [
            'class' => 'form-control mb-3 form-select',
            'required' => true,
            'name' => 'nationality'
        ]
    ],
    'department' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">Departamento <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'department',
            'id' => 'lista_departamento'
        ]
    ],
    'municipality' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">Municipio <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'municipality',
            'id' => 'municipios'
        ]
    ],
    'address' => [
        'type' => 'text',
        'label' => '<label class="form-label mb-2">Dirección <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'address',
            'id' => 'address',
            'placeholder' => 'Ingrese su dirección' // Placeholder para la dirección
        ]
    ],
    'people_charge' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">Personas a cargo <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'people_charge',
        ],
        'options' => [
            '' => 'Seleccione',
            '0' => '0',
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
            '7' => '7',
            '8' => '8',
            '9' => '9',
            '10' => '10',
            'Más de 10' => 'Más de 10'
        ]
    ],

    'vulnerable_population' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">¿Pertenece a un grupo poblacional reconocido por sus necesidades especiales o de atención prioritaria? <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',
            'Sí' => 'Sí',
            'No' => 'No'
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'vulnerable_population',
            'id' => 'vulnerable_population'  // Asegúrate de agregar un id para poder seleccionarlo con JavaScript
        ]
    ],
    'vulnerable_type' => [
        'type' => 'select',  // Cambié esto para que sea un select
        'label' => '<label class="form-label mb-2">¿Podría indicar a cuál de los siguientes grupos pertenece?</label>',
        'options' => [
            '' => 'Seleccione',
            'Madre/ padre cabeza de hogar' => 'Madre/ padre cabeza de hogar',
            'Desplazado por la violencia' => 'Desplazado por la violencia',
            'Adulto mayor' => 'Adulto mayor',
            'Victima del conflicto armado' => 'Victima del conflicto armado',
            'Algún tipo de Discapacidad' => 'Algún tipo de Discapacidad',
            'Población Negra' => 'Población Negra',
            'Raizales y Palenqueros' => 'Raizales y Palenqueros',
            'Afrocolombianos' => 'Afrocolombianos',
            'Población indigena' => 'Población indigena',
            'No aplica' => 'No aplica'
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'name' => 'vulnerable_type',
            'id' => 'vulnerable_type',  // Agrega un id también aquí para seleccionarlo con JavaScript

        ]
    ],
    'ethnic_group' => [
        'type' => 'select',  // Cambié esto para que sea un select
        'label' => '<label class="form-label mb-2">Grupo étnico <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',
            'Indígena' => 'Indígena',
            'Raizal del Archipielago de San Andres y Providencia y Santa Catalina' => 'Raizal del Archipielago de San Andres y Providencia y Santa Catalina',
            'Gitano (Rom)' => 'Gitano (Rom)',
            'Palanquero de San Basilio' => 'Palanquero de San Basilio',
            'Negro' => 'Negro',
            'Mulato' => 'Mulato',
            'Afrodescendiente' => 'Afrodescendiente',
            'Afrocolombiano' => 'Afrocolombiano',
            'No aplica' => 'No aplica'
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'ethnic_group',
            'id' => 'ethnic_group',  // Agrega un id también aquí para seleccionarlo con JavaScript
        ]
    ],
    'stratum' => [
        'type' => 'radio',
        'label' => '<label class="form-label mb-2">Estrato <span class="required">*</span></label>',
        'options' => [
            '1' => '1',
            '2' => '2',
            '3' => '3',
            '4' => '4',
            '5' => '5',
            '6' => '6',
            'Sin especificar' => 'Sin especificar'
        ],
        'attributes' => [
            'class' => 'form-check-input',  // Clase Bootstrap para los radio buttons
            'required' => true,
            'name' => 'stratum'
        ]
    ],

    'residence_area' => [
        'type' => 'select',
        'label' => '<label class="form-label mt-3">Área de residencia <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',
            'Urbana' => 'Urbana',
            'Rural' => 'Rural'
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'residence_area',
            'id' => 'residence_area'  // Añadido ID para JavaScript
        ]
    ],
    'country_person' => [
        'type' => 'select',
        'label' => '<label class="form-label mt-3">¿Es usted campesino? <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',
            'Sí' => 'Sí',
            'No' => 'No'
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'country_person',
            'id' => 'country_person'
        ]
    ],
    'training_level' => [
        'type' => 'select',  // Cambié esto para que sea un select
        'label' => '<label class="form-label mb-2">Nivel de formación <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',
            'Sin escolaridad' => 'Sin escolaridad',
            'Primaria (hasta 5°)' => 'Primaria (hasta 5°)',
            'Secundaria (Hasta 9°)' => 'Secundaria (Hasta 9°)',
            'Media (Bachiller)' => 'Media (Bachiller)',
            'Técnico' => 'Técnico',
            'Tecnológico' => 'Tecnológico',
            'Pregrado' => 'Pregrado',
            'Especialización' => 'Especialización',
            'Maestria' => 'Maestria',
            'Doctorado' => 'Doctorado',

        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'training_level',
            'id' => 'training_level',  // Agrega un id también aquí para seleccionarlo con JavaScript
        ]
    ],
    'occupation' => [
        'type' => 'select',  // Cambié esto para que sea un select
        'label' => '<label class="form-label mb-2">Ocupación <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',
            'Empleado' => 'Empleado',
            'Independiente' => 'Independiente',
            'Emprendedor' => 'Emprendedor',
            'Estudiante' => 'Estudiante',
            'Ama de casa' => 'Ama de casa',
            'Pensionado' => 'Pensionado',
            'Desemplado' => 'Desemplado',

        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'occupation',
            'id' => 'occupation',  // Agrega un id también aquí para seleccionarlo con JavaScript
        ]
    ],
    'time_obligations' => [
        'type' => 'select',  // Cambié esto para que sea un select
        'label' => '<label class="form-label mb-2">¿Qué tiempo te consume tus obligaciones? <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',
            'Tiempo completo' => 'Tiempo completo',
            'Medio tiempo' => 'Medio tiempo',
            'Tiempo parcial' => 'Tiempo parcial',

        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'time_obligations',
            'id' => 'time_obligations',  // Agrega un id también aquí para seleccionarlo con JavaScript
        ]
    ],
    'motivations_belong_program' => [
        'type' => 'text',
        'label' => '<label class="form-label mb-2">¿Cuáles son las motivaciones para pertenecer al programa? (Puede marcar varias opciones) <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'motivations_belong_program',
        ]
    ],
    'current_situation' => [
        'type' => 'textarea',
        'label' => '<label class="form-label mt-3">¿Cuál es tu situación actual? (Puede marcar varias opciones) <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'current_situation'
        ]
    ],
    'impediment_complete_course' => [
        'type' => 'select',
        'label' => '<label class="form-label mt-3">¿Qué consideras como un impedimento para concluir el curso? <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione', // Opción inicial 
            'Conseguir empleo' => 'Conseguir empleo',
            'Carga académica' => 'Carga académica',
            'Dificultades económicas' => 'Dificultades económicas',
            'Compromisos Sociales' => 'Compromisos Sociales',
            'Familia y/o entorno' => 'Familia y/o entorno',
            'Condiciones de salud' => 'Condiciones de salud',
            'Incompatibilidad con los horarios' => 'Incompatibilidad con los horarios',
            'Ninguna de las anteriores' => 'Ninguna de las anteriores',

        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'impediment_complete_course'
        ]
    ],

    'availability' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">¿Cuenta con disponibilidad de 14 horas semanales para la formación? <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione', // Opción inicial 
            'Sí' => 'Sí',
            'No' => 'No',
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'availability'
        ]
    ],
    'mode' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">Modalidad <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione', // Opción inicial
            'Presencial' => 'Presencial',
            //'Virtual' => 'Virtual (Clases en vivo)',
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'mode',
            'id' => 'mode'  // ID necesario para manipulación en JS

        ]
    ],
    'headquarters' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">Seleccione la sede de elección <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione', // Opción inicial
            'Paloquemao' => 'Paloquemao',
            'Chapinero calle 67' => 'Chapinero calle 67',
            'Chapinero carrera 20' => 'Chapinero carrera 20',
            'No aplica' => 'No aplica',

        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'headquarters',
            'id' => 'headquarters'  // ID necesario para manipulación en JS
        ]
    ],

    'program' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">Programa de interes <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione', // Opción inicial
            'Análisis de datos' => 'Análisis de datos',
            'Ciberseguridad' => 'Ciberseguridad',
            'Inteligencia Artificial' => 'Inteligencia Artificial',
            'Programación' => 'Programación',
            'BlockChain' => 'BlockChain',
            'Arquitectura en la nube' => 'Arquitectura en la nube',
            'Robótica y automatización' => 'Robótica y automatización',
            'Internet de las cosas - IoT' => 'Internet de las cosas - IoT'
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'program',
            'id' => 'program'
        ]
    ],

    'schedules' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">Horarios <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione primero una sede', // Opción inicial
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'schedules',
            'id' => 'schedules'
        ]
    ],

    'schedules_alternative' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">Horario alternativo <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione primero una modalidad',
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'schedules_alternative',
            'id' => 'schedules_alternative'
        ]
    ],

    'prior_knowledge' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">¿Tienes conocimientos previos de tecnología o programación? <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccionar',
            'Sí' => 'Sí',
            'No' => 'No'
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'prior_knowledge',
            'id' => 'prior_knowledge'  // ID necesario para manipulación en JS
        ]
    ],
    'level' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">Nivel<span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccionar',
            'Explorador' => 'Explorador (Conocimientos básicos)',
            'Integrador' => 'Integrador (Conocimientos intermedios)',
            'Innovador' => 'Innovador (Conocimientos avanzados)',
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'level',
            'id' => 'level'  // ID necesario para manipulación en JS
        ]
    ],

    'languages' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">Además del español, ¿tienes conocimiento de otro idioma? <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccionar',
            'Inglés' => 'Inglés',
            'Francés' => 'Francés',
            'Alemán' => 'Alemán',
            'Italiano' => 'Italiano',
            'Portugués' => 'Portugués',
            'Otro' => 'Otro',
            'Ningúno' => 'Ningúno'
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'languages',
            'id' => 'languages'  // ID necesario para manipulación en JS
        ]
    ],

    'languages_level' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">De acuerdo a la anterior pregunta ¿Qué nivel? <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccionar',
            'Básico' => 'Básico',
            'Intermedio' => 'Intermedio',
            'Avanzado' => 'Avanzado',
            'No aplica' => 'No aplica',
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'languages_level',
            'id' => 'languages_level',
            'value' => 'No aplica'  // Por defecto "No aplica"
        ]
    ],


    'medical_condition' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">¿Presentas actualmente una condición médica? <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',  // Opción por defecto
            'Sí' => 'Sí',  // Opción para responder afirmativamente
            'No' => 'No',  // Opción para responder negativamente
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'medical_condition',
            'id' => 'medical_condition'  // ID necesario para manipulación en JS si es necesario
        ]
    ],

    'disability' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">¿Tiene alguna condición de discapacidad? <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',  // Opción por defecto
            'Sí' => 'Sí',  // Opción para responder afirmativamente
            'No' => 'No',  // Opción para responder negativamente
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'disability',
            'id' => 'disability'  // ID necesario para manipulación en JS si es necesario
        ]
    ],
    'type_disability' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">¿Podría informarnos sobre el tipo de discapacidad con la que cuenta? <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',  // Opción por defecto
            'Discapacidad visual' => 'Discapacidad visual',  // Opción para responder afirmativamente
            'Discapacidad auditiva' => 'Discapacidad auditiva',  // Opción para responder negativamente
            'Sordoceguera' => 'Sordoceguera',  // Opción para responder afirmativamente
            'Discapacidad intelectual' => 'Discapacidad intelectual',  // Opción para responder negativamente
            'Discapacidad psicosocial' => 'Discapacidad psicosocial (mental)',  // Opción para responder afirmativamente
            'Discapacidad física' => 'Discapacidad física',  // Opción para responder negativamente
            'Discapacidad múltiple' => 'Discapacidad múltiple',  // Opción para responder negativamente
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'type_disability',
            'id' => 'type_disability'  // ID necesario para manipulación en JS si es necesario
        ]
    ],

    'pregnancy' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">¿Se encuentra en estado de embarazo? <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',  // Opción por defecto
            'Sí' => 'Sí',
            'No' => 'No'
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'pregnancy'
        ]
    ],
    'technologies' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">¿Cuenta con alguno(s) de los siguientes equipos? <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',  // Opción por defecto
            'computador' => 'Computador',
            'tablet' => 'Tablet',
            'smartphone' => 'Smartphone'
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'technologies'
        ]
    ],
    'internet' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">¿Cuenta con internet? <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',  // Opción por defecto
            'Sí' => 'Sí',
            'No' => 'No'
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'internet'
        ]
    ],
    'knowledge_program' => [
        'type' => 'select',
        'label' => '<label class="form-label mb-2">¿Cómo te enteraste del programa? <span class="required">*</span></label>',
        'options' => [
            '' => 'Seleccione',  // Opción por defecto
            'Familiares y amigos' => 'Familiares y amigos',
            'Redes sociales' => 'Redes sociales',
            'Mintic' => 'Mintic',
            'Radio' => 'Radio',
            'Prensa' => 'Prensa',
            'Otro' => 'Otro'
        ],
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'knowledge_program'
        ]
    ],
    'accept_requirements' => [
        'type' => 'checkbox',
        'label' => '',
        'options' => [
            'Sí' => 'Acepta los requisitos establecidos por la presente convocatoria',
        ],
        'attributes' => [
            'class' => 'form-check-input mb-3',
            'required' => true,
        ],
        'inline' => true  // Para que se muestre en una sola línea
    ],
    'accepts_tech_talent' => [
        'type' => 'checkbox',
        'label' => '',
        'options' => [
            'Sí' => 'Acepta la carta de compromiso de talento Tech',
        ],
        'attributes' => [
            'class' => 'form-check-input mb-3',
            'required' => true,
        ],
        'inline' => true  // Para que se muestre en una sola línea
    ],
    'accept_data_policies' => [
        'type' => 'checkbox',
        'label' => '',
        'options' => [
            'Sí' => 'Confirmo que he leído y acepto las políticas de tratamiento de datos personales',
        ],
        'attributes' => [
            'class' => 'form-check-input mb-3',
            'required' => true,
        ],
        'inline' => true  // Para que se muestre en una sola línea
    ],
    'file_front_id' => [
        'type' => 'file',
        'label' => '<label class="form-label mb-2">Adjunte aquí la cara frontal de su documento de identidad <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'file_front',
            'id' => 'file_front',
            'accept' => '.pdf, .doc, .jpg, .jpeg, .png',
            'style' => 'display: none;',  // Ocultamos el input tradicional
        ],
    ],
    'file_back_id' => [
        'type' => 'file',
        'label' => '<label class="form-label mb-2">Adjunte aquí el reverso de su documento de identidad <span class="required">*</span></label>',
        'attributes' => [
            'class' => 'form-control mb-3',
            'required' => true,
            'name' => 'file_front',
            'id' => 'file_front',
            'accept' => ' .jpg, .jpeg, .png',
            'style' => 'display: none;',  // Ocultamos el input tradicional
        ],
    ],


    // Definir los nuevos campos (como ocultos)
    'has_certification' => [
        'type' => 'hidden',
        'attributes' => [
            'id' => 'has_certification',
            'name' => 'has_certification'
        ]
    ],
    'program_certified' => [
        'type' => 'hidden',
        'attributes' => [
            'id' => 'program_certified',
            'name' => 'program_certified'
        ]
    ],
];
