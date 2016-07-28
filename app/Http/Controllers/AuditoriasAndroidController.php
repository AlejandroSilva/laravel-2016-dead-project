<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

class AuditoriasAndroidController extends Controller {
    // GET api/auditorias-android/auditoria/{idAuditoria}/ird
    public function api_auditoria_ird($idAuditoria){
        return response()->json([
            'ceco' => 84,
            'cliente' => 'FCV',
            'direccion' => 'dmaskkdsksdkak #1233',
            'comuna' => 'Providencia',
            'fecha' => '2016-08-03',
            'productosRDI' => [
                [
                    'sku'=>'102378',
                    'descripcion' => 'H.SHO.SH.CITRUS FRESH 400',
                    'laboratorio' => 'PROCTER-GILLETTE',
                    'categoria' => 'SHAMPOO',
                    'barras' => ['7590002025628','17590002025625', '1023985', '7590002047279']
                ],[
                    'sku'=>'254858',
                    'descripcion' => 'VEXA CD COM.28',
                    'laboratorio' => 'CHILE RECETARIO',
                    'categoria' => 'ANTICONCEPTIVO ORAL Y VAGINAL',
                    'barras' => ['7800007698681','7800007714046']
                ],[
                    'sku'=>'258911',
                    'descripcion' => 'PLAIS.EDP.VA.HOT BLACK.80',
                    'laboratorio' => 'PETRIZZIO',
                    'categoria' => 'FRAGANCIAS FEMENINAS',
                    'barras' => ['7804907815629', '17804907815626']
                ],[
                    'sku'=>'264376',
                    'descripcion' => 'LISTERINE ZERO C.MINT 500',
                    'laboratorio' => 'JOHNSON CONSUMO',
                    'categoria' => 'ENJUAGUES BUCALES',
                    'barras' => ['4785907', '1789101061945', '7891010619459', '7891010974336', '10312547428320']
                ],[
                    'sku'=>'267692',
                    'descripcion' => 'SLIMTONE YOGU/BER.BAR(8)',
                    'laboratorio' => 'VIVE+',
                    'categoria' => 'NUTRIGEN	SUPLEMENTO ALIMENTICIO',
                    'barras' => ['7804609251978', '17804609251968', '17804609251975', '7804609251954']
                ],[
                    'sku'=>'267863',
                    'descripcion' => 'PAMPERS JUEG/SUEN XXGX112',
                    'laboratorio' => 'PROCTER-GILLETTE',
                    'categoria' => 'PA?ALES DE BEBE',
                    'barras' => ['7506309821153', '17506309821150']
                ],[
                    'sku'=>'268577',
                    'descripcion' => 'SENSODYNE REPA.EX.FRES100',
                    'laboratorio' => 'GLAXO PERFUMERIA',
                    'categoria' => 'DESSENSIBILIZANTE DENTAL',
                    'barras' => ['7580008', '7794640170737', '17794640170734', '299C-34/13']
                ],[
                    'sku'=>'268996',
                    'descripcion' => 'ORAL-B 3DWHITE LUXE 100G',
                    'laboratorio' => 'PROCTER-GILLETTE',
                    'categoria' => 'PASTAS DENTALES',
                    'barras' => ['7506295371519', '17506295371516']
                ],[
                    'sku'=>'271378',
                    'descripcion' => 'H.SHO.SH.MEN OLDSPICE400M',
                    'laboratorio' => 'PROCTER-GILLETTE',
                    'categoria' => 'SHAMPOO',
                    'barras' => ['7506309894492', '17506309894499']
                ],[
                    'sku'=>'272649',
                    'descripcion' => 'A.BRAVA AZUL EDT.E/L.100M',
                    'laboratorio' => 'PUIG',
                    'categoria' => 'FRAGANCIAS MASCULINAS',
                    'barras' => ['8411061797143', '8411061817384', '18411061797140']
                ],[
                    'sku'=>'111049',
                    'descripcion' => 'INFOR CAP. 30',
                    'laboratorio' => 'PRATER',
                    'categoria' => 'REVITALIZANTE',
                    'barras' => ['4173310', '7804918400524', '27804918400528', '57804918400529']
                ],[
                    'sku'=>'262526',
                    'descripcion' => 'AXASOL OV.VAG.500MG.2',
                    'laboratorio' => 'SILESIA ETICO',
                    'categoria' => 'ANTIINFECCIOSO VAGINAL',
                    'barras' => ['781501', '7800020167218', '17800020167215', 'F-17930']
                ],[
                    'sku'=>'263650',
                    'descripcion' => 'BLEPHAGEL DUO GEL OFT.40G',
                    'laboratorio' => 'ANDROMACO ETICO',
                    'categoria' => 'HUMECTANTE OCULAR',
                    'barras' => ['1135630', '6006340001176', '16006340001173', '369C-50']
                ],[
                    'sku'=>'199252',
                    'descripcion' => 'PENICIL.SOD.AMP.1000000U*',
                    'laboratorio' => 'GENERICOS VARIOS',
                    'categoria' => 'PENICILINOTERAPIA',
                    'barras' => [
                        '7800018600826', '13617', '6288220', '7800007136176', '7800007665409',
                        '7800026002209', '7800086205305', '7804620831807', '17800007136173',
                        '17800026002206', '17804620831804'
                    ]
                ],[
                    'sku'=>'260006',
                    'descripcion' => 'MILLEFIORI BARRA DEP.38GR',
                    'laboratorio' => 'COSMETICA NACIONAL',
                    'categoria' => 'DEPILACION',
                    'barras' => ['7804923036749', '17804923036746']
                ],[
                    'sku'=>'199445',
                    'descripcion' => 'NAPROXENO COM.550MG.10',
                    'laboratorio' => 'GENERICOS VARIOS',
                    'categoria' => 'ANALGESICO-ANTIINFLAMATORIO',
                    'barras' => ['17639']
                ],[
                    'sku'=>'205167',
                    'descripcion' => 'VICHY DES.SP.A/TRANSP.125',
                    'laboratorio' => 'LOREAL VICHY LA ROCHE',
                    'categoria' => 'DESODORANTES',
                    'barras' => ['5328748']
                ],[
                    'sku'=>'23013',
                    'descripcion' => 'CIDOTEN AMP.4,0MG. 1',
                    'laboratorio' => 'SCHERING PLOUGH',
                    'categoria' => 'CORTICOTERARIA SISTEMICA',
                    'barras' => ['7800007176394']
                ],[
                    'sku'=>'34122',
                    'descripcion' => 'NIVEA  CR.SOFT 200ML.',
                    'laboratorio' => 'BEIERSDORF',
                    'categoria' => 'CREMAS MANOS Y CUERPO',
                    'barras' => ['7800018132242']
                ],[
                    'sku'=>'264185',
                    'descripcion' => 'BROMUR.IPRA.SOL.0,025%20M',
                    'laboratorio' => 'GENERICOS VARIOS',
                    'categoria' => 'BRONCODILATADOR INHALATORIO',
                    'barras' => ['7800046002951', '7800063111179', '17800007176391', '17800018132249', '17800046002958', '17800063111176', 'F-1837', '7700076002951']
                ]
            ],
            'productosFechaVencimiento' => [
                'pendiente por hacer' => '....'
            ]
        ]);
    }
}
