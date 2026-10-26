<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Promptable;

class IneExtractorAgent implements Agent, HasStructuredOutput
{
    use Promptable;

    public function instructions(): string
    {
        return <<<'PROMPT'
Eres un experto en lectura de credenciales para votar (INE) de México.

Analiza la imagen de la credencial y extrae únicamente la información visible.
No inventes, completes ni supongas datos. Si un dato no se distingue con claridad,
devuélvelo como null.

Reglas:
- "nombre" corresponde al nombre o nombres de pila.
- "apellido_paterno" y "apellido_materno" van separados.
- "domicilio" debe contener únicamente la calle y el número. No incluyas colonia,
  localidad, municipio, estado ni código postal.
- "colonia" y "localidad" van por separado.
- "seccion" corresponde únicamente a los dígitos de la sección electoral.
- "fecha_nacimiento" debe ir en formato YYYY-MM-DD.
- "curp" y "clave_elector" deben ir en mayúsculas y sin espacios.
- "sexo" debe ser "Masculino" o "Femenino". Si puedes leer la CURP, usa su
  carácter 11: H = Masculino y M = Femenino. Si no hay CURP, usa el campo
  SEXO de la credencial.
PROMPT;
    }

    public function schema(JsonSchema $schema): array
    {
        return [
            'nombre' => $schema->string()->nullable()
                ->description('Nombre o nombres de pila de la persona.'),
            'apellido_paterno' => $schema->string()->nullable()
                ->description('Apellido paterno.'),
            'apellido_materno' => $schema->string()->nullable()
                ->description('Apellido materno.'),
            'sexo' => $schema->string()->nullable()
                ->enum(['Masculino', 'Femenino'])
                ->description('Sexo inferido de la CURP o del campo SEXO de la credencial.'),
            'fecha_nacimiento' => $schema->string()->nullable()
                ->description('Fecha de nacimiento en formato YYYY-MM-DD.'),
            'curp' => $schema->string()->nullable()
                ->description('CURP en mayúsculas.'),
            'clave_elector' => $schema->string()->nullable()
                ->description('Clave de elector en mayúsculas.'),
            'domicilio' => $schema->string()->nullable()
                ->description('Calle y número, sin colonia, localidad, municipio ni código postal.'),
            'colonia' => $schema->string()->nullable()
                ->description('Colonia del domicilio.'),
            'localidad' => $schema->string()->nullable()
                ->description('Localidad del domicilio.'),
            'seccion' => $schema->string()->nullable()
                ->description('Solo los dígitos de la sección electoral.'),
        ];
    }
}
