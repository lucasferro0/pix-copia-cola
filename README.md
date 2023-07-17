
# lucasferro0/pix-copia-cola

Lib que possibilita gerar código copia e cola pix e decodificar um copia e cola.
Também é possível gerar o qrcode do copia e cola


# Documentação

```php
// Instalação

// execute o comando:
composer require lucasferro0/pix-copia-cola
```



## Uso/Exemplos

```php
require 'vendor/autoload.php'

use App\Package\PixCopiaCola\PixCopiaColaManager;

// Exemplo para decodificar um copia e cola

$copiaColaDecoded = (PixCopiaColaManager())->decode('copia_cola_aqui');

$copiaCola = $copiaColaDecoded->copiaCola;

$chavePix = $copiaColaDecoded->chavePix;

$valor = $copiaColaDecoded->valor;

$descricao = $copiaColaDecoded->descricao;

$identificador = $copiaColaDecode->identificador;

$nomeBeneficiario = $copiaColaDecoded->nomeBeneficiario;
```