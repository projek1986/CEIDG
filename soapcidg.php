<?php
header('Content-Type: text/html; charset=utf-8');
error_reporting(E_ERROR | E_WARNING | E_PARSE);
$arrLocales = array('pl_PL', 'pl','Polish_Poland.28592');
setlocale( LC_ALL, $arrLocales );
date_default_timezone_set('Europe/Warsaw');
set_time_limit(0);

function XML2Array(SimpleXMLElement $parent) {
    $array = array();
    foreach ($parent as $name => $element) {
        ($node = & $array[$name])
        && (1 === count($node) ? $node = array($node) : 1)
        && $node = & $node[];
        $node = $element->count() ? XML2Array($element) : trim($element);
    }
    return $array;
}

$wsdl = 'https://datastore.ceidg.gov.pl/CEIDG.DataStore/services/NewDataStoreProvider.svc?singleWsdl';
$Province = array('mazowieckie');
$token = '';
$MigrationDateFrom = '2017-07-04';
$MigrationDateTo = '2017-07-24';
$data = array(
    "AuthToken"  => $token,
    "Province"  => $Province,
    "MigrationDateFrom"   => $MigrationDateFrom,
    "MigrationDateTo"   => $MigrationDateTo,
);

$soap = new SoapClient($wsdl);
$resp = $soap->__soapCall("GetMigrationDataExtendedAddressInfo",array($data));

$array = json_decode(json_encode($resp), true);
//
$wpis = $array['GetMigrationDataExtendedAddressInfoResult'];
$xml = simplexml_load_string($wpis);
$xml_array = unserialize(serialize(json_decode(json_encode((array) $xml), 1)));
echo '<pre>';
//print_R($xml_array);



$to_db = array();

$i=0;
foreach ($xml_array['InformacjaOWpisie'] as $xml){

   $to_db[$i]['IdentyfikatorWpisu']= $xml['IdentyfikatorWpisu'];
   $to_db[$i]['Imie']= $xml['DanePodstawowe']['Imie'];
   $to_db[$i]['Nazwisko']= $xml['DanePodstawowe']['Nazwisko'];

   if($xml['DanePodstawowe']['NIP'][0] == '') {

       $to_db[$i]['NIP']= '';
   }else {
       $to_db[$i]['NIP']= $xml['DanePodstawowe']['NIP'];

   }

    if($xml['DanePodstawowe']['REGON'][0] == '') {

        $to_db[$i]['REGON']= '';
    }else {

        $to_db[$i]['REGON'] = $xml['DanePodstawowe']['REGON'];
    }


    if($xml['DanePodstawowe']['Firma'][0] == '') {

        $to_db[$i]['Firma']= '';
    }else {

        $to_db[$i]['Firma']= $xml['DanePodstawowe']['Firma'];
    }


    if($xml['DaneKontaktowe']['AdresPocztyElektronicznej'][0] == '') {

        $to_db[$i]['AdresPocztyElektronicznej']= '';
    }else {

        $to_db[$i]['AdresPocztyElektronicznej']= $xml['DaneKontaktowe']['AdresPocztyElektronicznej'];


    }

    if($xml['DaneKontaktowe']['Telefon'][0] == '') {

        $to_db[$i]['Telefon']= '';
    }else {
        $to_db[$i]['Telefon']= $xml['DaneKontaktowe']['Telefon'];
    }

    if($xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Miejscowosc'][0] == '') {

        $to_db[$i]['Miejscowosc']= '';
    }else {
        $to_db[$i]['Miejscowosc']= $xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Miejscowosc'];
    }

    if($xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Ulica'][0] == '') {

        $to_db[$i]['Ulica']= '';
    }else {
        $to_db[$i]['Ulica']= $xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Ulica'];
    }

    if($xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Budynek'][0] == '') {

        $to_db[$i]['Budynek']= '';
    }else {
        $to_db[$i]['Budynek']= $xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Budynek'];
    }

    if($xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Lokal'][0] == '') {

        $to_db[$i]['Lokal']= '';
    }else {
        $to_db[$i]['Lokal']= $xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Lokal'];
    }

    if($xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['KodPocztowy'][0] == '') {

        $to_db[$i]['KodPocztowy']= '';
    }else {
        $to_db[$i]['KodPocztowy']= $xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['KodPocztowy'];
    }


    if($xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Poczta'][0] == '') {

        $to_db[$i]['Poczta']= '';
    }else {
        $to_db[$i]['Poczta']= $xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Poczta'];
    }

    if($xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Gmina'][0] == '') {

        $to_db[$i]['Gmina']= '';
    }else {
        $to_db[$i]['Gmina']= $xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Gmina'];
    }

    if($xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Powiat'][0] == '') {

        $to_db[$i]['Powiat']= '';
    }else {
        $to_db[$i]['Powiat']= $xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Powiat'];
    }

    if($xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Wojewodztwo'][0] == '') {

        $to_db[$i]['Wojewodztwo']= '';
    }else {
        $to_db[$i]['Wojewodztwo']= $xml['DaneAdresowe']['AdresGlownegoMiejscaWykonywaniaDzialalnosci']['Wojewodztwo'];
    }

    if($xml['DaneDodatkowe']['DataRozpoczeciaWykonywaniaDzialalnosciGospodarczej'][0] == '') {

        $to_db[$i]['DataRozpoczeciaWykonywaniaDzialalnosciGospodarczej']= '';
    }else {
        $to_db[$i]['DataRozpoczeciaWykonywaniaDzialalnosciGospodarczej']= $xml['DaneDodatkowe']['DataRozpoczeciaWykonywaniaDzialalnosciGospodarczej'];
    }


    if($xml['DaneDodatkowe']['MalzenskaWspolnoscMajatkowa'][0] == '') {

        $to_db[$i]['MalzenskaWspolnoscMajatkowa']= '';
    }else {
        $to_db[$i]['MalzenskaWspolnoscMajatkowa']= $xml['DaneDodatkowe']['MalzenskaWspolnoscMajatkowa'];
    }

    if($xml['DaneDodatkowe']['Status'][0] == '') {

        $to_db[$i]['Status']= '';
    }else {
        $to_db[$i]['Status']= $xml['DaneDodatkowe']['Status'];
    }

    if($xml['DaneDodatkowe']['KodyPKD'][0] == '') {

        $to_db[$i]['KodyPKD']= '';
    }else {
        $to_db[$i]['KodyPKD']= $xml['DaneDodatkowe']['KodyPKD'];

    }


    $i++;

}


//var_dump($to_db);

include('connect_db.php');


foreach ($to_db as $valuedb) {


    $datatodb = array(

         $valuedb['IdentyfikatorWpisu'],
       $valuedb['Imie'],
        $valuedb['Nazwisko'],
        $valuedb['NIP'],
        $valuedb['REGON'],
        $valuedb['Firma'],
       $valuedb['AdresPocztyElektronicznej'],
       $valuedb['Telefon'],
        $valuedb['Miejscowosc'],
        $valuedb['Budynek'],
        $valuedb['Lokal'],
       $valuedb['KodPocztowy'],
        $valuedb['Poczta'],
        $valuedb['Gmina'],
        $valuedb['Powiat'],
       $valuedb['Wojewodztwo'],
        $valuedb['DataRozpoczeciaWykonywaniaDzialalnosciGospodarczej'],
        $valuedb['MalzenskaWspolnoscMajatkowa'],
        $valuedb['Status'],
       $valuedb['KodyPKD'],


    );



//
//    $sql = "INSERT INTO users (IdentyfikatorWpisu , Imie  , Nazwisko , NIP , REGON , Firma , AdresPocztyElektronicznej , Telefon , Miejscowosc , Budynek , Lokal , KodPocztowy , Poczta , Gmina , Powiat , Wojewodztwo , DataRozpoczeciaWykonywaniaDzialalnosciGospodarczej , MalzenskaWspolnoscMajatkowa , Status , KodyPKD  )
//
// VALUES (? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,? ,?  )";
//    $conn->prepare($sql)->execute($datatodb);
//
//


}







//
//$wsdl = 'https://datastoretest.ceidg.gov.pl/CEIDG.DataStore/Services/NewDataStoreProvider.svc?singleWsdl';
//$data = array(
//    "AuthToken" => "xxx",
//    "NIP" => "6332212511"
//);
//$soap = new SoapClient($wsdl, array('trace' => true, 'exception' => true));
//
//$soap->__getTypes();
//$soap->__getFunctions();
//$response = $soap->__soapCall("GetMigrationDataExtendedAddressInfo", array($data));
//print_r($response);


//
//try {
//    $url = 'https://datastore.ceidg.gov.pl/CEIDG.DataStore/services/NewDataStoreProvider.svc?singleWsdl';
//
//    $api_key = 'o041TbI8xTzHzvHyy3viZeWSMV5SJ4h7TIiKT5quqAVkkgo2gTssPEk7OACZJRY3';
//    $nip = '5840778201';
//    $client = new SoapClient($url, array("trace" => 1, "exception" => 0));
//    $xml = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/"
//            xmlns:tem="http://tempuri.org/"
//            xmlns:arr="http://schemas.microsoft.com/2003/10/Serialization/Arrays">
//            <soapenv:Header/>
//            <soapenv:Body>
//            <tem:GetMigrationDataExtendedAddressInfo>
//            <tem:AuthToken>'.$api_key.'</tem:AuthToken>
//            <tem:NIP>
//            <arr:string>'.$nip.'</arr:string>
//            </soapenv:Body>
//            </soapenv:Envelope>
//            ';
//    $soapBody = new \SoapVar($xml, \XSD_ANYXML);
//    $result = $client->__soapCall('GetMigrationDataExtendedAddressInfo', array($soapBody));
//    var_dump($result, $client->__getFunctions(), $soapBody);
//} catch (SoapFault $exception) {
//    echo $exception->getMessage();
//
//    echo 'tutaj';
//}


//
//$client = new SoapClient("https://datastoretest.ceidg.gov.pl/CEIDG.DataStore/services/NewDataStoreProvider.svc?singleWsdl");
//
//
//echo '<pre>';
//var_dump($client->__getFunctions());
//
//echo 'typy<br>';
//var_dump($client->__getTypes());
//
//echo 'response<br>';
//var_dump($client->__getLastResponse());
//
//echo 'response<br>';
//var_dump($client->__getLastResponse());

