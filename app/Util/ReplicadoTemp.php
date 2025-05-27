<?php
namespace App\Util;

use App\Models\Vinculo;
use Uspdev\Replicado\DB;
use Uspdev\Replicado\Pessoa;

class ReplicadoTemp
{
    
    /**
     * Método que lista docentes e servidores de uma unidade;
     *
     * @return Array lista de docentes e servidores com dados como nompesttd (nome social) e codpes (numero USP)
     *
     */
    public static function listarServidoresDocentes(){

        $codundclg = getenv('REPLICADO_CODUNDCLG');
        $param = [
            'codundclg' => $codundclg,
        ];
        
        $query = "SELECT L.*, P.* 
                    FROM LOCALIZAPESSOA L INNER JOIN PESSOA P ON (L.codpes = P.codpes)
                    WHERE L.codundclg IN (:codundclg) AND L.sitatl = 'A' AND L.codfncetr = 0 
                    AND L.tipvinext IN ('Servidor', 'Docente') 
                    ORDER BY P.nompesttd";


        return DB::fetchAll($query, $param);
    }
 
}