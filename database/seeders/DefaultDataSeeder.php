<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{Departamento, Municipio, Sexo};

class DefaultDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear departamento "Sin asignación"
        $departamentoSinAsignacion = Departamento::firstOrCreate([
            'code' => '00',
            'name' => 'Sin asignación'
        ]);

        // Crear municipio "Sin asignación" nacional
        Municipio::firstOrCreate([
            'departamento_id' => $departamentoSinAsignacion->id,
            'name' => 'Sin asignación'
        ], [
            'code' => '0000' // Código especial para nacional
        ]);

        // Crear registros de sexo básicos
        Sexo::firstOrCreate(
        ['code' => 'H'],
        ['description' => 'HOMBRE']
        );

        Sexo::firstOrCreate(
        ['code' => 'M'],
        ['description' => 'MUJER']
        );

        // Datos de departamentos y municipios
        $datos = [
            '01' => [
                'name' => 'ATLANTIDA',
                'municipios' => [
                    '01' => 'LA CEIBA',
                    '02' => 'EL PORVENIR',
                    '03' => 'ESPARTA',
                    '04' => 'JUTIAPA',
                    '05' => 'LA MASICA',
                    '06' => 'SAN FRANCISCO',
                    '07' => 'TELA',
                    '08' => 'ARIZONA',
                ]
            ],
            '02' => [
                'name' => 'COLON',
                'municipios' => [
                    '01' => 'TRUJILLO',
                    '02' => 'BALFATE',
                    '03' => 'IRIONA',
                    '04' => 'LIMON',
                    '05' => 'SABA',
                    '06' => 'SANTA FE',
                    '07' => 'SANTA ROSA AGUAN',
                    '08' => 'SONAGUERA',
                    '09' => 'TOCOA',
                    '10' => 'BONITO ORIENTAL',
                ]
            ],
            '03' => [
                'name' => 'COMAYAGUA',
                'municipios' => [
                    '01' => 'COMAYAGUA',
                    '02' => 'AJUTERIQUE',
                    '03' => 'EL ROSARIO',
                    '04' => 'ESQUIAS',
                    '05' => 'HUMUYA',
                    '06' => 'LA LIBERTAD',
                    '07' => 'LAMANI',
                    '08' => 'LA TRINIDAD',
                    '09' => 'LEJAMANI',
                    '10' => 'MEAMBAR',
                    '11' => 'MINAS DE ORO',
                    '12' => 'OJOS DE AGUA',
                    '13' => 'SAN JERONIMO',
                    '14' => 'SAN JOSE DE COMAYAGUA',
                    '15' => 'SAN JOSE DEL POTRERO',
                    '16' => 'SAN LUIS',
                    '17' => 'SAN SEBASTIAN',
                    '18' => 'SIGUATEPEQUE',
                    '19' => 'VILLA DE SAN ANTONIO',
                    '21' => 'TAULABE',
                ]
            ],
            '04' => [
                'name' => 'COPAN',
                'municipios' => [
                    '01' => 'SANTA ROSA DE COPAN',
                    '02' => 'CABAÑAS',
                    '04' => 'COPAN RUINAS',
                    '05' => 'CORQUIN',
                    '06' => 'CUCUYAGUA',
                    '07' => 'DOLORES',
                    '08' => 'DULCE NOMBRE',
                    '09' => 'EL PARAISO',
                    '10' => 'FLORIDA',
                    '11' => 'LA JIGUA',
                    '12' => 'LA UNION',
                    '13' => 'NUEVA ARCADIA',
                    '15' => 'SAN ANTONIO',
                    '16' => 'SAN JERONIMO',
                    '19' => 'SAN NICOLAS',
                    '20' => 'SAN PEDRO',
                    '21' => 'SANTA RITA',
                    '23' => 'VERACRUZ',
                ]
            ],
            '05' => [
                'name' => 'CORTES',
                'municipios' => [
                    '01' => 'SAN PEDRO SULA',
                    '02' => 'CHOLOMA',
                    '03' => 'OMOA',
                    '04' => 'PIMIENTA',
                    '05' => 'POTRERILLOS',
                    '06' => 'PUERTO CORTES',
                    '07' => 'SAN ANTONIO DE CORTES',
                    '08' => 'SAN FRANCISCO DE YOJOA',
                    '09' => 'SAN MANUEL',
                    '10' => 'SANTA CRUZ DE YOJOA',
                    '11' => 'VILLANUEVA',
                    '12' => 'LA LIMA',
                ]
            ],
            '06' => [
                'name' => 'CHOLUTECA',
                'municipios' => [
                    '01' => 'CHOLUTECA',
                    '02' => 'APACILAGUA',
                    '03' => 'CONCEPCION DE MARIA',
                    '04' => 'DUYURE',
                    '05' => 'EL CORPUS',
                    '06' => 'EL TRIUNFO',
                    '07' => 'MARCOVIA',
                    '08' => 'MOROLICA',
                    '09' => 'NAMASIGUE',
                    '10' => 'OROCUINA',
                    '11' => 'PESPIRE',
                    '12' => 'SAN ANTONIO DE FLORES',
                    '13' => 'SAN ISIDRO',
                    '14' => 'SAN JOSE',
                    '15' => 'SAN MARCOS DE COLON',
                    '16' => 'SANTA ANA DE YUSGUARE',
                ]
            ],
            '07' => [
                'name' => 'EL PARAISO',
                'municipios' => [
                    '01' => 'YUSCARAN',
                    '02' => 'ALAUCA',
                    '03' => 'DANLI',
                    '04' => 'EL PARAISO',
                    '06' => 'JACALEAPA',
                    '08' => 'MOROCELI',
                    '09' => 'OROPOLI',
                    '10' => 'POTRERILLOS',
                    '11' => 'SAN ANTONIO DE FLORES',
                    '13' => 'SAN MATIAS',
                    '14' => 'SOLEDAD',
                    '15' => 'TEUPASENTI',
                    '16' => 'TEXIGUAT',
                    '17' => 'VADO ANCHO',
                    '18' => 'YAUYUPE',
                    '19' => 'TROJES',
                ]
            ],
            '08' => [
                'name' => 'FRANCISCO MORAZAN',
                'municipios' => [
                    '01' => 'DISTRITO CENTRAL',
                    '02' => 'ALUBAREN',
                    '03' => 'CEDROS',
                    '04' => 'CURAREN',
                    '05' => 'EL PORVENIR',
                    '06' => 'GUAIMACA',
                    '07' => 'LA LIBERTAD',
                    '08' => 'LA VENTA',
                    '09' => 'LEPATERIQUE',
                    '10' => 'MARAITA',
                    '11' => 'MARALE',
                    '12' => 'NUEVA ARMENIA',
                    '13' => 'OJOJONA',
                    '15' => 'REITOCA',
                    '16' => 'SABANAGRANDE',
                    '17' => 'SAN ANTONIO DE ORIENTE',
                    '19' => 'SAN IGNACIO',
                    '20' => 'CANTARRANAS',
                    '21' => 'SAN MIGUELITO',
                    '22' => 'SANTA ANA',
                    '23' => 'SANTA LUCIA',
                    '25' => 'TATUMBLA',
                    '26' => 'VALLE DE ANGELES',
                    '27' => 'VILLA SAN FRANCISCO',
                    '28' => 'VALLECILLOS',
                ]
            ],
            '09' => [
                'name' => 'GRACIAS A DIOS',
                'municipios' => [
                    '04' => 'JUAN FRANCISCO BULNES',
                    '06' => 'WAMPUSIRPI',
                ]
            ],
            '10' => [
                'name' => 'INTIBUCA',
                'municipios' => [
                    '03' => 'COLOMONCAGUA',
                    '04' => 'CONCEPCION',
                    '06' => 'INTIBUCA',
                    '07' => 'JESUS DE OTORO',
                    '08' => 'MAGDALENA',
                    '09' => 'MASAGUARA',
                    '10' => 'SAN ANTONIO',
                    '11' => 'SAN ISIDRO',
                    '12' => 'SAN JUAN',
                    '15' => 'SANTA LUCIA',
                    '16' => 'YAMARANGUILA',
                    '17' => 'SAN FRANCISCO DE OPALACA',
                ]
            ],
            '11' => [
                'name' => 'ISLAS DE LA BAHIA',
                'municipios' => [
                    '01' => 'ROATAN',
                    '02' => 'GUANAJA',
                    '03' => 'JOSE SANTOS GUARDIOLA',
                    '04' => 'UTILA',
                ]
            ],
            '12' => [
                'name' => 'LA PAZ',
                'municipios' => [
                    '03' => 'CABAÑAS',
                    '04' => 'CANE',
                    '05' => 'CHINACLA',
                    '06' => 'GUAJIQUIRO',
                    '09' => 'MERCEDES DE ORIENTE',
                    '10' => 'OPATORO',
                    '12' => 'SAN JOSE',
                    '13' => 'SAN JUAN',
                    '14' => 'SAN PEDRO DE TUTULE',
                    '15' => 'SANTA ANA',
                    '16' => 'SANTA ELENA',
                    '17' => 'SANTA MARIA',
                    '18' => 'SANTIAGO DE PURINGLA',
                    '19' => 'YARULA',
                ]
            ],
            '13' => [
                'name' => 'LEMPIRA',
                'municipios' => [
                    '01' => 'GRACIAS',
                    '04' => 'COLOLACA',
                    '06' => 'GUALCINCE',
                    '08' => 'LA CAMPA',
                    '09' => 'LA IGUALA',
                    '10' => 'LAS FLORES',
                    '11' => 'LA UNION',
                    '12' => 'LA VIRTUD',
                    '13' => 'LEPAERA',
                    '14' => 'MAPULACA',
                    '15' => 'PIRAERA',
                    '16' => 'SAN ANDRES',
                    '17' => 'SAN FRANCISCO',
                    '19' => 'SAN MANUEL COLOHETE',
                    '20' => 'SAN RAFAEL',
                    '21' => 'SAN SEBASTIAN',
                    '22' => 'SANTA CRUZ',
                    '23' => 'TALGUA',
                    '26' => 'VALLADOLID',
                    '28' => 'SAN MARCOS DE CAIQUIN',
                ]
            ],
            '14' => [
                'name' => 'OCOTEPEQUE',
                'municipios' => [
                    '01' => 'OCOTEPEQUE',
                    '02' => 'BELEN GUALCHO',
                    '03' => 'CONCEPCION',
                    '04' => 'DOLORES MERENDON',
                    '06' => 'LA ENCARNACION',
                    '07' => 'LA LABOR',
                    '09' => 'MERCEDES',
                    '10' => 'SAN FERNANDO',
                    '11' => 'SAN FRANCISCO DEL VALLE',
                    '12' => 'SAN JORGE',
                    '13' => 'SAN MARCOS',
                    '15' => 'SENSENTI',
                ]
            ],
            '15' => [
                'name' => 'OLANCHO',
                'municipios' => [
                    '01' => 'JUTICALPA',
                    '02' => 'CAMPAMENTO',
                    '04' => 'CONCORDIA',
                    '05' => 'DULCE NOMBRE DE CULMI',
                    '07' => 'ESQUIPULAS DEL NORTE',
                    '08' => 'GUALACO',
                    '09' => 'GUARIZAMA',
                    '10' => 'GUATA',
                    '11' => 'GUAYAPE',
                    '12' => 'JANO',
                    '13' => 'LA UNION',
                    '14' => 'MANGULILE',
                    '15' => 'MANTO',
                    '16' => 'SALAMA',
                    '17' => 'SAN ESTEBAN',
                    '19' => 'SAN FRANCISCO DE LA PAZ',
                    '21' => 'SILCA',
                    '22' => 'YOCON',
                ]
            ],
            '16' => [
                'name' => 'SANTA BARBARA',
                'municipios' => [
                    '01' => 'SANTA BARBARA',
                    '02' => 'ARADA',
                    '03' => 'ATIMA',
                    '05' => 'CEGUACA',
                    '06' => 'SAN JOSE DE COLINAS',
                    '07' => 'CONCEPCION DEL NORTE',
                    '09' => 'CHINDA',
                    '10' => 'EL NISPERO',
                    '11' => 'GUALALA',
                    '12' => 'ILAMA',
                    '16' => 'PETOA',
                    '19' => 'SAN FRANCISCO DE OJUERA',
                    '20' => 'SAN LUIS',
                    '22' => 'SAN NICOLAS',
                    '23' => 'SAN PEDRO DE ZACAPA',
                    '24' => 'SANTA RITA',
                    '26' => 'TRINIDAD',
                    '27' => 'LAS VEGAS',
                    '28' => 'NUEVA FRONTERA',
                ]
            ],
            '17' => [
                'name' => 'VALLE',
                'municipios' => [
                    '01' => 'NACAOME',
                    '02' => 'ALIANZA',
                    '03' => 'AMAPALA',
                    '04' => 'ARAMECINA',
                    '05' => 'CARIDAD',
                    '06' => 'GOASCORAN',
                    '07' => 'LANGUE',
                    '08' => 'SAN FRANCISCO DE CORAY',
                    '09' => 'SAN LORENZO',
                ]
            ],
            '18' => [
                'name' => 'YORO',
                'municipios' => [
                    '01' => 'YORO',
                    '02' => 'ARENAL',
                    '03' => 'EL NEGRITO',
                    '09' => 'SULACO',
                    '10' => 'VICTORIA',
                    '11' => 'YORITO',
                ]
            ],
        ];

        foreach ($datos as $depCode => $depData) {
            $departamento = Departamento::firstOrCreate(
                ['code' => $depCode],
                ['name' => $depData['name']]
            );

            foreach ($depData['municipios'] as $muniCode => $muniName) {
                Municipio::firstOrCreate(
                    [
                        'code' => $depCode . str_pad($muniCode, 2, '0', STR_PAD_LEFT),
                        'departamento_id' => $departamento->id
                    ],
                    [
                        'name' => $muniName
                    ]
                );
            }
        }

        $this->command->info('Datos por defecto creados exitosamente.');
    }
}
