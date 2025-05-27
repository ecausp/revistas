<?php
namespace App\Util;

use App\Models\Vinculo;
use Uspdev\Replicado\DB;
use Uspdev\Replicado\Pessoa;

class ReplicadoTemp
{
    
    public static function listarServidoresEDocentes(){

        $codundclg = getenv('REPLICADO_CODUNDCLG');

        $query = "SELECT DISTINCT P.codpes, P.nompesttd
                    FROM PESSOA P
                    INNER JOIN LOCALIZAPESSOA L ON L.codpes = P.codpes
                    WHERE L.codundclg IN (:codundclg)
                    AND L.sitatl IN ('A')
                    AND EXISTS (
                        SELECT 1
                        FROM LOCALIZAPESSOA V
                        WHERE V.codpes = P.codpes
                        AND V.tipvinext IN ('Servidor', 'Docente')
                    )
                    ORDER BY P.nompesttd ASC";

        $param = [
            'codundclg' => $codundclg,
        ];
        
        return DB::fetchAll($query, $param);
    }
 
}