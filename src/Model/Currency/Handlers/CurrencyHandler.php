<?php


namespace App\Handlers;


use App\Entity\Currency;
use App\Repository\CurrencyRepository;
use Doctrine\ORM\EntityManager;

class CurrencyHandler
{
    private $dateofadded;

    public function getDateofadded(): ?\DateTimeInterface
    {
        return $this->dateofadded;
    }

    public function setDateofadded(\DateTimeInterface $dateofadded): self
    {
        $this->dateofadded = $dateofadded;

        return $this;
    }

    public function getCbrData(CurrencyRepository $currencyRepository, EntityManager $entityManager): bool
    {
        $arr = $currencyRepository->findBy(['dateofadded' => $this->getDateofadded()]);
        if (!$arr) {
            $currencies = simplexml_load_file('http://www.cbr.ru/scripts/XML_daily.asp?date_req=' . $this->getDateofadded()->format('d/m/Y'));
            //dump($currencies);
            if ($currencies->Valute)
                foreach ($currencies->Valute as $valute) {
                    $currency = new Currency();
                    $currency->setDateofadded($this->getDateofadded());
                    $currency->setCharCode($valute->CharCode);
                    $currency->setName(mb_convert_encoding($valute->Name, 'UTF-8'));
                    $currency->setNumCode($valute->NumCode);
                    $currency->setNominal(intval($valute->Nominal));
                    $currency->setValue(str_replace(",", ".", $valute->Value));
                    $currency->setValuteId($valute->attributes()->ID);
                    $entityManager->persist($currency);
                }
            $entityManager->flush();
            return true;
        }

        return false;
    }
}