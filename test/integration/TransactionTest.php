<?php
require_once('test/bootstrap.php');
class TransactionIntegrationTest extends PHPUnit_Framework_TestCase
{

    /**
     * Runs once before all tests are started     *
     */
    public function setUp()
    {
        $options = array(
            'username' => 'pj-ql-01',
            'password' => 'pj-ql-01p',
            'appkey' => '2489d40d-a74f-474f-9e8e-7b39507f3101'
        );

        parent::setUp();
        $this->client = new TransactionClient($options);
    }

    /**
     * Used to test whether a transaction is successful. asserts that the provided transaction is free of errors, the approved value is true and status is capture
     */
    private function isSuccessfulTransaction($transaction,$type = null)
    {

        $this->assertObjectNotHasAttribute('errors',$transaction,$type . " Transaction was not successful, It contained errors.");
        $this->assertTrue($transaction->response->approved, $type . " Transaction was not approved");
        $this->assertEquals($transaction->status,"CAPTURE",$type . " Transaction was not a capture");
    }

    /**
     * @description returns a random number with two decimal places and no commas
     * @return string
     */
    private function getRandomAmountBase()
    {
        return number_format(rand(1,100),2,'.','');
    }

    /**
     * @description create an ach transaction
     */
    public function testACHTransaction()
    {
        $data = array('achRoutingNumber' => '104000016',
            'achAccountNumber' => '123456789',
            'achAccountType' => 'CHECKING',
            'achType' => 'PPD',
            'amountBase' => $this->getRandomAmountBase(),
        );

        $transaction = $this->client->create($data);

        $this->isSuccessfulTransaction($transaction,'ACH');

    }


    /**
     * @description create a credit card transaction
     */
    public function testCardTransactionCreate()
    {
        $data = array(
            'cardNumber' => '4444333322221111',
            'cardExpMonth' => '01',
            'cardExpYear' => '18',
            'cardCvv' => '999',
            'amountBase' => $this->getRandomAmountBase()
        );

        $transaction = $this->client->create($data);
        $this->isSuccessfulTransaction($transaction,'Card');
    }

    /**
     * @description create a keyed credit card transaction
     */
    public function testKeyedCardTransactionCreate()
    {
        $data = array(
            'cardTrack' => '%B4444333322221111^First/Last^1712980100000?;4444333322221111=1712980100000?',
            'amountBase' => $this->getRandomAmountBase()
        );
        $this->isSuccessfulTransaction($this->client->create($data),'Keyed');
    }

    /**
     * @description create and void a transaction
     */
    public function testVoidTransaction()
    {
        $data = array('achRoutingNumber' => '104000016',
            'achAccountNumber' => '123456789',
            'achAccountType' => 'CHECKING',
            'achType' => 'PPD',
            'amountBase' => $this->getRandomAmountBase(),
        );
        $transaction = $this->client->create($data);

        $update_data = array(
            'status' => 'VOID'
        );

        $transaction = $this->client->update($transaction->transactionId,$update_data);
        $this->assertEquals('VOID',$transaction->status,"Transaction was not voided");
        $this->assertObjectNotHasAttribute('errors',$transaction," Transaction has errors");
        $this->assertTrue($transaction->response->approved," Transaction did not maintain an approved status");

    }


    /**
     * @description create a transaction and add a signature to it
     */
    public function testAddSignature()
    {
        $data = array('achRoutingNumber' => '104000016',
            'achAccountNumber' => '123456789',
            'achAccountType' => 'CHECKING',
            'achType' => 'PPD',
            'amountBase' => $this->getRandomAmountBase(),
        );
        $transaction = $this->client->create($data);

        $signature_data = array(
            'signature' => '}SCRIPTEL A ST1501-PYJ 01.00.08 #_5,#<5\'#<5z#"5\'#"5=#"6t#"7m#<7/#>8,#:9\'#+a\'#|b\'$Mcz$Mdv$Meo$Me\'$Mfm$Mfq$Mfr$Mfq$Me[$New$Od-$Pc[$Qco$Sbr$Uat$W9t$Z8v$>7z$+6=%M6w%Q6q%T6n%V6o%Y6s%"6\'%_7o%?7,%+8s%{8[%|9z^Maq^Na-^Nbp^Obv^Obx^Oby^Obv^Pbq^Qa/^Rau^Sam^U9-^V9x^Y9v^"9u^>9v^+9x&M9,&Q9=&Uao&Zat&>ay&{a-*Pa/*Ta/*Xa-*<az*?as*|9[(O9w(R8=(U8r(X7,(Z7q("6/(<6z(<6w("6u("6t(Z6t(Y6t(X6v(W6\'(U7m(R7\'(P8u(M9q*|9[*{ay*|a\(Mbt(Pby(Sbz(Wbx("bs(?a[({axAO9\AS9xAV9nAY8-A"8xA_8wA>8yA?8/A?9rA?9/A?awA>boA<b/AZcyAWdsARemAMe/(?fx(Zgo(Tgx(Pg-*|g-*?gz*_gr*"f;*Yfr*Ye\'*Zd\*<dv*?c[*|cz(Pct(Vcr(<cs({cuAQcxAXcyA>czBMcxBTcvBYcrB>cmB|b.CQbvCUbmCXa\'C<aoC?9\'C+9nC|8yDM7\DM7wDM6[DM6xDM6qC|5[C{5/C+5.C:5=C>6qC_6,C<7qC<7.C_8rC_8.C>9sC?9;C+awC{bmDMbzDPcmDScvDVc-DYc[D<doD>dsD:dxD{d-D{d[D|etD{e.D:frD>f-D"gqDWgyDSg;DMhmC>hnCYg\CUg-CQgtCNf;CMfqCMevCNd-CQdoCTc\'CXctC<cpC+cmDPb[DVb[D<b\D|coERcrEXcuE>cyFMc-FSc[FXdqF_dtF+dvGOdvGRdrGVdoGYc;G<cxG?cpG+b.G{btG|a/G|asG|9.G{9rG:8;G>8xGZ8sGW8qGS8qGO8sF:8yF"8[FV9xFRaoFOa/FNbxFMcpFMc.FNdtFQd/FTerFXe\'F<e\F+fqGOfuGTfvGYftG>fpG|e[HPe\'HSeuHWenHZd.H_dwH:dqH|c\IOc;IRc.IVc-IYc.I_c;I:c[I|doJOdrJRdvJUdzJXd/J"enJ_euJ:e\'J|e[KOfrKRfwKUfzKWf,KXf-KWf-KVf,KSf\'KOfzJ:fyJYfxJRfxI+fyIYfzIQfzH+fzHZfxHVfvHSfsHQfqHPfoHPfmHPe[HQe/HRe\'HTewHVerHZenH>d=IMd-ISdzIYdxI?dxJNdxJSdzJWd-J"d=J>eoJ:etJ{ezJ|e/KNe\KOfqKPftKQfvKRfxKSfyKTfxKVfvKWftKWfrKXfq ]',
        );
        $transaction = $this->client->addSignature($transaction->transactionId,$signature_data);

        $this->assertEquals('SIGNED',$transaction->signatureStatus,'The transaction does not have a signed status');

    }

    /**
     * @description create a transaction and then read it
     */
    public function testReadTransaction()
    {
        $data = array('achRoutingNumber' => '104000016',
            'achAccountNumber' => '123456789',
            'achAccountType' => 'CHECKING',
            'achType' => 'PPD',
            'amountBase' => $this->getRandomAmountBase(),
        );
        $transaction = $this->client->create($data);
        $read_transaction = $this->client->read($transaction->transactionId);

        $this->assertEquals($transaction->transactionId,$read_transaction->transactionId,'The created transaction Id is not the same as the read transaction Id');

    }


}
