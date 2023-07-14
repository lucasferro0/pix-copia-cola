<?php

namespace App\Package\PixCopiaCola;

use Piggly\Pix\Parser;
use Piggly\Pix\Reader;
use Webmozart\Assert\Assert;
use Piggly\Pix\StaticPayload;
use Illuminate\Support\Facades\Http;

class PixCopiaColaParser
{
    private CopiaColaDecoded $copiaColaDecoded;

    public function __construct()
    {
        $this->copiaColaDecoded = new CopiaColaDecoded();
    }

    public function decode(string $copiaCola): CopiaColaDecoded
    {
        $reader  = new Reader($copiaCola);

        $this->copiaColaDecoded->copiaCola = $reader->getRaw();
        $this->copiaColaDecoded->chavePix = $reader->getPixKey();
        $this->copiaColaDecoded->valor = $reader->getAmount();
        $this->copiaColaDecoded->descricao = $reader->getDescription();
        $this->copiaColaDecoded->identificador = $reader->getTid() ?? '***';


        if (!$this->copiaColaDecoded->chavePix) {
            $url = $reader->getMPM()->getEmv('26')->getField(25)->getValue(); // Url para buscar o o bearer token

            Assert::notNull($url, 'Copia e cola inválido.');

            $response = Http::get($url);

            Assert::true($response->successful(), 'Copia e cola inválido.');

            $bearerToken = $response->body();

            [$header, $payload, $secret] = explode('.', $bearerToken);

            $dadosPayload = base64_decode($payload);

            $dadosArray = json_decode($dadosPayload, true);

            $valorOriginal = (float) ($dadosArray['valor']['original'] ?? 0);
            $valorJuros = (float) ($dadosArray['valor']['juros'] ?? 0);
            $valorMulta = (float) ($dadosArray['valor']['multa'] ?? 0);
            $valorAbatimento = (float) ($dadosArray['valor']['abatimento'] ?? 0);

            $valorAtualizado = round(
                ($valorOriginal + $valorJuros + $valorMulta) - $valorAbatimento,
                2
            );

            $chavePix = $dadosArray['chave'];

            $this->copiaColaDecoded->chavePix = $chavePix;
            $this->copiaColaDecoded->valor = $valorAtualizado;
        }

        return $this->copiaColaDecoded;
    }

    public function gerarPixCopiaColaEstatico(
        string $chavePix,
        float $valor,
        ?string $descricao,
        string $nomeBeneficiario,
        string $cidade
    ): string {
        $keyType = Parser::getKeyType($chavePix);

        if ($descricao) {
            $payload = (new StaticPayload())
                            ->setAmount($valor)
                            ->setTid('***')
                            ->setDescription($descricao)
                            ->setPixKey($keyType, $chavePix)
                            ->setMerchantName($nomeBeneficiario)
                            ->setMerchantCity($cidade);
        } else {
            $payload = (new StaticPayload())
                            ->setAmount($valor)
                            ->setTid('***')
                            ->setPixKey($keyType, $chavePix)
                            ->setMerchantName($nomeBeneficiario)
                            ->setMerchantCity($cidade);
        }



        return $payload->getPixCode();
    }
}
