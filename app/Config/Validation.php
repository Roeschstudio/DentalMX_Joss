<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Validation\StrictRules\CreditCardRules;
use CodeIgniter\Validation\StrictRules\FileRules;
use CodeIgniter\Validation\StrictRules\FormatRules;
use CodeIgniter\Validation\StrictRules\Rules;

class Validation extends BaseConfig
{
    // --------------------------------------------------------------------
    // Setup
    // --------------------------------------------------------------------

    /**
     * Stores the classes that contain the
     * rules that are available.
     *
     * @var list<string>
     */
    public array $ruleSets = [
        Rules::class,
        FormatRules::class,
        FileRules::class,
        CreditCardRules::class,
        \App\Validation\CustomRules::class, // Reglas personalizadas
    ];

    /**
     * Specifies the views that are used to display the
     * errors.
     *
     * @var array<string, string>
     */
    public array $templates = [
        'list'   => 'CodeIgniter\Validation\Views\list',
        'single' => 'CodeIgniter\Validation\Views\single',
        'dental_errors' => 'errors/validation_list', // Template personalizado
    ];

    // --------------------------------------------------------------------
    // Rules
    // --------------------------------------------------------------------
    
    /**
     * Reglas de validación para pacientes
     */
    public array $pacientes = [
        'nombre' => [
            'rules' => 'required|min_length[3]|max_length[100]|alpha_space',
            'errors' => [
                'required' => 'El nombre es obligatorio',
                'min_length' => 'El nombre debe tener al menos 3 caracteres',
                'max_length' => 'El nombre no puede exceder 100 caracteres',
                'alpha_space' => 'El nombre solo puede contener letras y espacios'
            ]
        ],
        'primer_apellido' => [
            'rules' => 'required|min_length[3]|max_length[100]|alpha_space',
            'errors' => [
                'required' => 'El primer apellido es obligatorio',
                'min_length' => 'El primer apellido debe tener al menos 3 caracteres',
                'max_length' => 'El primer apellido no puede exceder 100 caracteres',
                'alpha_space' => 'El primer apellido solo puede contener letras y espacios'
            ]
        ],
        'segundo_apellido' => [
            'rules' => 'permit_empty|max_length[100]|alpha_space',
            'errors' => [
                'max_length' => 'El segundo apellido no puede exceder 100 caracteres',
                'alpha_space' => 'El segundo apellido solo puede contener letras y espacios'
            ]
        ],
        'email' => [
            'rules' => 'permit_empty|valid_email|max_length[255]|unique_email[pacientes.email,{id}]',
            'errors' => [
                'valid_email' => 'Ingrese un correo electrónico válido',
                'max_length' => 'El correo no puede exceder 255 caracteres',
                'unique_email' => 'Este correo electrónico ya está registrado'
            ]
        ],
        'celular' => [
            'rules' => 'required|exact_length[10]|numeric|phone_valid',
            'errors' => [
                'required' => 'El número celular es obligatorio',
                'exact_length' => 'El celular debe tener exactamente 10 dígitos',
                'numeric' => 'El celular solo debe contener números',
                'phone_valid' => 'Ingrese un número celular válido'
            ]
        ],
        'domicilio' => [
            'rules' => 'required|min_length[10]|max_length[255]|safe_chars',
            'errors' => [
                'required' => 'El domicilio es obligatorio',
                'min_length' => 'El domicilio debe tener al menos 10 caracteres',
                'max_length' => 'El domicilio no puede exceder 255 caracteres',
                'safe_chars' => 'El domicilio contiene caracteres no permitidos'
            ]
        ],
        'nacionalidad' => [
            'rules' => 'permit_empty|max_length[100]|alpha_space',
            'errors' => [
                'max_length' => 'La nacionalidad no puede exceder 100 caracteres',
                'alpha_space' => 'La nacionalidad solo puede contener letras y espacios'
            ]
        ]
    ];

    /**
     * Reglas de validación para usuarios/auth
     */
    public array $auth = [
        'email' => [
            'rules' => 'required|valid_email|max_length[255]',
            'errors' => [
                'required' => 'El correo electrónico es obligatorio',
                'valid_email' => 'Ingrese un correo electrónico válido',
                'max_length' => 'El correo no puede exceder 255 caracteres'
            ]
        ],
        'password' => [
            'rules' => 'required|min_length[8]|strong_password',
            'errors' => [
                'required' => 'La contraseña es obligatoria',
                'min_length' => 'La contraseña debe tener al menos 8 caracteres',
                'strong_password' => 'La contraseña debe contener mayúsculas, minúsculas y números'
            ]
        ]
    ];

    /**
     * Reglas de validación para medicamentos
     */
    public array $medicamentos = [
        'nombre' => [
            'rules' => 'required|min_length[3]|max_length[100]|alpha_numeric_space',
            'errors' => [
                'required' => 'El nombre del medicamento es obligatorio',
                'min_length' => 'El nombre debe tener al menos 3 caracteres',
                'max_length' => 'El nombre no puede exceder 100 caracteres',
                'alpha_numeric_space' => 'El nombre solo puede contener letras, números y espacios'
            ]
        ],
        'descripcion' => [
            'rules' => 'max_length[500]|safe_chars',
            'errors' => [
                'max_length' => 'La descripción no puede exceder 500 caracteres',
                'safe_chars' => 'La descripción contiene caracteres no permitidos'
            ]
        ],
        'precio' => [
            'rules' => 'required|decimal|greater_than[0]',
            'errors' => [
                'required' => 'El precio es obligatorio',
                'decimal' => 'Ingrese un precio válido',
                'greater_than' => 'El precio debe ser mayor a 0'
            ]
        ],
        'stock' => [
            'rules' => 'required|integer|greater_than_equal_to[0]',
            'errors' => [
                'required' => 'El stock es obligatorio',
                'integer' => 'El stock debe ser un número entero',
                'greater_than_equal_to' => 'El stock no puede ser negativo'
            ]
        ]
    ];

    /**
     * Reglas de validación para servicios
     */
    public array $servicios = [
        'nombre' => [
            'rules' => 'required|min_length[3]|max_length[100]|alpha_numeric_space',
            'errors' => [
                'required' => 'El nombre del servicio es obligatorio',
                'min_length' => 'El nombre debe tener al menos 3 caracteres',
                'max_length' => 'El nombre no puede exceder 100 caracteres',
                'alpha_numeric_space' => 'El nombre solo puede contener letras, números y espacios'
            ]
        ],
        'descripcion' => [
            'rules' => 'max_length[500]|safe_chars',
            'errors' => [
                'max_length' => 'La descripción no puede exceder 500 caracteres',
                'safe_chars' => 'La descripción contiene caracteres no permitidos'
            ]
        ],
        'precio' => [
            'rules' => 'required|decimal|greater_than[0]',
            'errors' => [
                'required' => 'El precio es obligatorio',
                'decimal' => 'Ingrese un precio válido',
                'greater_than' => 'El precio debe ser mayor a 0'
            ]
        ],
        'duracion' => [
            'rules' => 'required|integer|greater_than[0]',
            'errors' => [
                'required' => 'La duración es obligatoria',
                'integer' => 'La duración debe ser un número entero',
                'greater_than' => 'La duración debe ser mayor a 0'
            ]
        ]
    ];
}
