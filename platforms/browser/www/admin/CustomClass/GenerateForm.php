<?php
//require_once('fpdf.php');
require_once('tfpdf.php');
class GenerateForm extends tFPDF {
	
	var $inputData=array();
	var $isAdmin=0;
	var $addPath='';
	var $yAfterBlockLeft=0;
	var $yAfterBlock=0;
	
	var $yBeforeColored=0;
	
	var $currentPage=0;
	
	var $tmpFiles = array(); 
	
	var $encrypted;          //whether document is protected
    var $Uvalue;             //U entry in pdf document
    var $Ovalue;             //O entry in pdf document
    var $Pvalue;             //P entry in pdf document
    var $enc_obj_id;         //encryption object id
    var $last_rc4_key;       //last RC4 key encrypted (cached for optimisation)
    var $last_rc4_key_c;     //last RC4 computed key
    var $header=1;
    
    var $isTemp=false;
    
    var $rec;
    
    var $totalNoPDV=0;
    var $totalPDV=0;
    var $totalWidthPDV;
    
    var $lastPage;
    
    var $deliveryPaper=false;
    var $deliveryDeadline='';
    
    var $size1=10;
    var $size2=12;
    var $size3=16;
    
    
    function GenerateForm($orientation='P', $unit='mm', $format='A4')
    {
        parent::tFPDF($orientation, $unit, $format);

        $this->encrypted=false;
        $this->last_rc4_key='';
        $this->padding="\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08".
                        "\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
    }
	
	function _convert($str) {
		return $str;
    } 
	
	function dottedLine($max=200){
		$content='';
		for($i=0; $i<$max; $i++){
			$content .='. ';
		}
		return  $content;
	}
	
	function checkIsNewPage($add=0){
		if (($this->GetY()+$add) >= 259) {
		    $this->AddPage();
		    $y = 10; // should be your top margin
		    $this->SetY($y);
		}
	}
        
        function Footer($curX=10){
            $curX=10;
            $this->setXY($curX,-10);
            $this->Line($curX, $this->GetY(), 200, $this->GetY());
            $this->Ln(0);
            $this->setFont('', '', 8);
            $this->SetTextColor(0, 0, 0);
            $this->Cell(0, 5, '11290 Pine Valley Drive, Woodbridge, ON, L4L 1A6', 0, 1, 'C');
        }
	
	function Header(){
            if ($this->header == 1)
            {
                
            }
        }
        
        private function getSingleSCItemPriceNoRabat($rec1){
             require_once 'ProductCategoriesFE.php';
            $rec=new ProductCategoriesFE();
            return $rec->getSingleSCItemPriceNoRabat($rec1);
        }
        
        private function getProducts($id){
            require_once 'ProductsFE.php';
            $rec=new ProductsFE();
            return $rec->getProductsByIDP($id);
        }
	
	
	function createHeader($isTemp=false){
		$this->SetMargins(10, 10, 10);
                $this->setFont('', 'B', $this->size3);
                $this->Cell(0, 6, 'KRISTUS DĀRZS', 0, 1, 'C');
                $this->setFont('', 'B', $this->size2);
                $this->Cell(0, 6, 'LATVIAN FOUNDATION', 0, 1, 'C');
                $this->Ln(6);
                $this->setLineWidth(0.5);
                $this->Line($this->getX(), $this->GetY(), 200, $this->GetY());
                $this->Ln(5);
                $this->setLineWidth(0.1);
	}
        
        function wrapSummaryChoosenFormat($rec, $isAdmin=false, $x=0, $width1=10, $lheight=2){
           $content=''; 
            $counter1=0;
            $content .=$isAdmin===false?$rec['formatstextshort']:strip_tags(preg_replace('/\<\/div\>/', chr(10), $rec['formatstext']));
           
            return $content;
        }
        
        function wrapSummaryChoosenFormat2($rec, $isAdmin=false, $x=0, $width1=10, $lheight=2){
           $content=''; 
            $counter1=0;
            $content .=strip_tags(preg_replace(array('/\<\/div\>/', '/\<br\s\/\>/'), array(chr(10), chr(10)), $rec['propertiestext']));
           
            return $content;
        }
        
        function wrapFormats($rec, $x=40, $isAdmin=false){
            $this->setFont('', '', 5);
            $this->MultiCell($x+5, 2, $this->wrapSummaryChoosenFormat($rec, $isAdmin), 0, 'L');
            $this->setFont('', '', 7);
        }
        
        function createTopHeaderInovice($isTemp=false){
            $this->SetTextColor(0, 0, 0);
            $curY=$this->GetY()+3;
            $this->setY($curY);
            $this->setFont('', 'B', 10);
            $lineheight=4;
            $lineheight1=$lineheight+3;
            if($_SESSION['whatUserOnDetails']->iscompany==1){
                $this->Cell(0, $lineheight1, $_SESSION['whatUserOnDetails']->companyname, 0, 1, 'L');
            }else{
                $this->Cell(0, $lineheight1, $_SESSION['whatUserOnDetails']->firstname . ' ' . $_SESSION['whatUserOnDetails']->lastname, 0, 1, 'L');
            }
            $this->setFont('', '', 9);
            $this->Cell(0, $lineheight, $_SESSION['whatUserOnDetails']->address . ', ' . $_SESSION['whatUserOnDetails']->postalnumber . ' ' . $_SESSION['whatUserOnDetails']->city, 0, 1, 'L');
            if($_SESSION['whatUserOnDetails']->contactperson!=''){
                $this->Cell(0, $lineheight, 'n/r ' . $_SESSION['whatUserOnDetails']->contactperson, 0, 1, 'L');
            }
            if($_SESSION['whatUserOnDetails']->iscompany==1){
                $this->Cell(0, $lineheight, 'ID: ' . $_SESSION['whatUserOnDetails']->idnumber, 0, 1, 'L');
                $this->Cell(0, $lineheight, 'PDV: ' . $_SESSION['whatUserOnDetails']->pdvnumber, 0, 1, 'L');
            }
            $this->Ln(10);
        }
        
       
        
        function createTopHeaderWorkSheet($rec){
            $this->SetTextColor(0, 0, 0);
            $curY=$this->GetY()+3;
            $this->setY($curY);
            $this->setFont('', 'B', $this->size2);
            $lineheight=4;
            $this->Cell(0, $lineheight, 'CHARITABLE DONATION RECEIPT', 0, 1, 'C');
            $this->Ln(10);
        }
        
        function createWorksheetHeading($rec, $curX=10){
            require_once 'Settings.php';
            $setting=new Settings();
            
            $this->SetTextColor(0, 0, 0);
            $curY=$this->GetY()+3;
            $this->setY($curY);
            $lineheight=5;
            $x=$curX;
            $x1=153;
            
            $this->SetTextColor(0, 0, 0);
            $this->setFont('', '', $this->size1);
            $this->SetX($x);
            $this->Cell(0, $lineheight, $rec->firstname . ' ' . $rec->lastname, 0, 0, 'L');
            $this->setFont('', 'B', $this->size1);
            $this->SetX($x1);
            $this->Cell(0, $lineheight, 'Donation Date:', 0, 0, 'L');
            $this->setFont('', '', $this->size1);
            $this->Cell(0, $lineheight, $rec->chkdate, 0, 1, 'R');
            $this->SetX($x);
            $this->Cell(0, $lineheight, $rec->address, 0, 0, 'L');
            $this->setFont('', 'B', $this->size1);
            $this->SetX($x1);
            $this->Cell(0, $lineheight, 'Receipt Number:', 0, 0, 'L');
            $this->setFont('', '', $this->size1);
            $this->Cell(0, $lineheight, $rec->receiptnumber, 0, 1, 'R');
            $this->SetX($x);
            $this->Cell(0, $lineheight, $rec->city . ' ' . $rec->postalcode, 0, 0, 'L');
            $this->setFont('', 'B', $this->size1);
            $this->SetX($x1);
            $this->Cell(0, $lineheight, 'Amount:', 0, 0, 'L');
            $this->setFont('', '', $this->size1);
            $this->Cell(0, $lineheight, CURRENCYSIGN . number_format($rec->amount, 2), 0, 1, 'R');
            $this->Ln(1);       
            $this->Line($curX, $this->GetY(), 200, $this->GetY());
            $this->Ln($lineheight*3);
            
            $this->SetTextColor(0, 0, 0);
            $this->setFont('', '', $this->size1);
            $this->SetX($x);
            $this->Cell(0, $lineheight, 'Date issued: ' . date('Y-m-d'), 0, 1, 'L');
            $this->Cell(0, $lineheight, 'Charity Reg. # 83549 0491 RR0001' . date('Y-m-d'), 0, 1, 'L');
            $this->Ln($lineheight);
        }
        
        function createInoviceHeading($isTemp=false, $curX=10){
            $this->SetTextColor(0, 0, 0);
            $curY=$this->GetY()+3;
            $this->setY($curY);
            $this->setFont('', 'B', 10);
            $lineheight=6;
            $x=124;
            $invoicenr=str_pad($this->rec->invoicenr, 4, '0', STR_PAD_LEFT);
            $invoiceyear=$this->rec->invoiceyear;
            $title=$this->deliveryPaper===true?'Otpremnica br. ' . $invoicenr . '/' . $invoiceyear:'Ponuda br. ' . $invoicenr . '/' . $invoiceyear;
            $title1=$this->deliveryPaper===true?'Mjesto i datum izdavanja otpremnice:':'Mjesto i datum izdavanja ponude:';
            $title3=$this->deliveryPaper===true?'Rok plaćanja: ' . $this->deliveryDeadline:'Ponuda važi 7 dana od dana izdavanja!';
            $title2=INVOICECITY . ', ' . date('d.m.Y');
            $this->Cell($x, $lineheight, $title, 0, 0, 'L');
            $this->setFont('', '', 8);
            $this->SetX($x);
            $y=$this->getY();
            $this->setY($y-4);
            $this->setFont('', 'B', 8);
            $this->SetTextColor(255, 0, 0);
            $this->Cell(0, $lineheight, $title3, 0, 1, 'R');
            $this->setXY($x, $y);
            $this->SetTextColor(0, 0, 0);
            $this->setFont('', '', 8);
            $this->Cell(0, $lineheight, $title1, 0, 0, 'L');
            $this->setFont('', 'B', 8);
            $this->Cell(0, $lineheight, $title2, 0, 1, 'R');
            $this->SetDrawColor(102, 102, 102);            
            $this->Line($curX, $this->GetY(), 200, $this->GetY());
            $this->Ln(1);
            $this->createInvoiceTitles($isTemp);
        }
        
        function createInvoiceTitlesWorksheet($isTemp=false, $curX=10){
            $this->SetTextColor(0, 0, 0);
            $curY=$this->GetY()+3;
            $this->setY($curY);
            $this->setFont('', 'B', 7);
            $lineheight=3;
            $x=9;
            $this->MultiCell($x, $lineheight, 'R. br.', 0, 'L');
            
            $this->setXY($curX+$x, $curY);
            $curX=$this->GetX();
            $x=140;            
            $this->MultiCell($x, $lineheight, 'Naziv', 0, 'L');
            
            $this->setXY($curX+$x, $curY);
            $curX=$this->GetX();
            $x=15;            
            $this->MultiCell($x, $lineheight, 'Jed. mjere', 0, 'C');
            
            $this->setXY($curX+$x+2, $curY);
            $curX=$this->GetX();
            $x=14;            
            $this->MultiCell($x, $lineheight, 'Količina', 0, 'R');
            
            $this->Ln(1);
            $curX=10;            
            $this->Line($curX, $this->GetY(), 200, $this->GetY());
            $this->Ln(1);
        }
        
        function createInvoiceTitles($isTemp=false, $curX=10){
            $this->SetTextColor(0, 0, 0);
            $curY=$this->GetY()+3;
            $this->setY($curY);
            $this->setFont('', 'B', 8);
            $lineheight=3;
            $x=0;
            
            $this->setXY($curX+$x, $curY);
            $curX=$this->GetX();
            $x=9;            
            $this->MultiCell($x, $lineheight, 'R.br.', 0, 'L');
            
            $this->setXY($curX+$x, $curY);
            $curX=$this->GetX();
            $x=66;            
            $this->MultiCell($x, $lineheight, 'Naziv usluge / proizvoda', 0, 'L');
            
            $this->setXY($curX+$x, $curY);
            $curX=$this->GetX();
            $x=20;            
            $this->MultiCell($x, $lineheight, 'Jed. mjere', 0, 'C');
            
            $this->setXY($curX+$x+2, $curY);
            $curX=$this->GetX();
            $x=15;            
            $this->MultiCell($x, $lineheight, 'Količina', 0, 'R');
            
            $this->setXY($curX+$x-2, $curY);
            $curX=$this->GetX();
            $x=25;            
            $this->MultiCell($x, $lineheight, 'Vrijednost', 0, 'R');
            
            $this->setXY($curX+$x+4, $curY);
            $curX=$this->GetX();
            $x=18;            
            $this->MultiCell($x, $lineheight, 'PDV (' . PDV . '%)', 0, 'R');
            
            $this->setXY(($curX+$x+5), $curY);
            $curX=$this->GetX();
            $x=27;            
            $this->MultiCell($x, $lineheight, 'Cijena bez PDV-a', 0, 'R');
            $this->Ln(1);
            $curX=10;            
            $this->Line($curX, $this->GetY(), 200, $this->GetY());
            $this->Ln(1);
        }
        
        
        
        function createTableInvoiceWorksheet($recS, $curX=10){
            $this->SetTextColor(0, 0, 0);
            $this->setFont('', '', $this->size1);
            $this->SetX($curX);
            $lineheight=5;
            $this->MultiCell(0, $lineheight, 'This is an official receipt for income tax purposes for your ' . date('Y', strtotime($recS->chkdate)) . ' donation . If you do not wish your name published, please contact Kristus Dārzs Latvian Home at (905) 832-3300.', 0, 'L');
            $this->Ln($lineheight);
            
            $this->MultiCell(0, $lineheight, 'For information on all registered charities in Canada under the Income Tax Act, please contact Canada Revenue Agency –', 0, 'L');
            $yPre=$this->getY();
            $y=$this->getY()-$lineheight;
            $this->setXY(25, $y);
            $this->SetTextColor(0, 0, 255);
            $this->setFont('', 'U', $this->size1);
            $this->Cell(0, 5, 'www.cra.gc.ca/charities', 0, 0, 'L', false, 'http://www.cra.gc.ca/charities');          $this->SetTextColor(0, 0, 0);
            $this->setFont('', '', $this->size1);
            $this->SetXY($curX, $yPre);
            $this->Ln($lineheight);
            $this->MultiCell(0, $lineheight, 'Paldies!  Thank you for your donation!', 0, 'L');
            $this->Ln($lineheight*3);
            $this->MultiCell(0, $lineheight, 'Authorized signature', 0, 'L');
            $this->Image($this->addPath . 'images/pdfSignature.png', $curX, $this->getY(), $this->PXToMM(130));
        }
	
        function createTableInvoice($isTemp=false, $curX=10){
            $rec=json_decode($this->rec->shoppingcart, true);
            if(sizeof($rec)>0){
                require_once 'Products.php';
                $product=new Products();
                //sum all rabats % if applied
                $sumAllRabats=$this->rec->rabats;
                
                $this->SetTextColor(0, 0, 0);
                $curY=$this->GetY()+0;
                $this->setY($curY);
                $this->setFont('', '', 7);
                $lineheight=6;
                $counter=1;
                foreach($rec as $key => $val){
                    $this->wrapSingleRow($val, $key, $product, $sumAllRabats, $counter, $curX);
                    $counter++;
                }
            }
        }
        
        function wrapFormats2($rec, $x=40, $isAdmin=false){
            require_once 'ProductValuesFE.php';
            $pv=new ProductValuesFE();
            $deliveryTime=$pv->getDeliveryTime($rec['product']);
            $postalWeight=$pv->getPostalWeight($rec['product']);
            $this->setFont('', '', 5);
            $this->MultiCell($x+5, 2, $this->wrapSummaryChoosenFormat2($rec, $isAdmin), 0, 'L');
            $this->setFont('', '', 7);
            $this->Ln(6);
            $this->Cell(0, 6, 'Težina (masa) isporuke: ' . number_format($postalWeight, 2, ',', '.') . ' ' . WEIGHTUNIT, 0, 1, 'L');
            $this->Cell(0, 6, 'Planirani rok isporuke: ' . date('d.m.Y.', time()+($deliveryTime+1)*ONEDAY), 0, 1, 'L');
        }
        
        
        
        
        function wrapSingleRowWorksheet($rec, $key, $product, $counter, $curX=10){  
            $lineheight=4;
            $curY=$this->GetY();
            $x=0;
            $this->SetTextColor(0, 0, 0);
            $this->setFont('', '', 8);
            
            $recP=$product->getProductDetails($key);
            if($recP!==false && sizeof($recP)>0){
                
                $qty=$product->getQuantityFromSC($rec['data'], $recP->id);
                
                $tempString='';
                $tempCounter=0;
                foreach($rec['data'] as $val){
                    if($val['tblid']==19 && $val['value']=='Custom'){
                        if(true===array_key_exists('valuewidth', $val) && true===array_key_exists('valueheight', $val)){
                            $val['value']=$val['valuewidth'] . ' x ' . $val['valueheight'] . ' mm';
                        }
                    }
                    if($tempCounter>0) $tempString .="\n";
                    $tempString .=$val['title'] . ': ' . $val['value'];  
                    $tempCounter++;
                }
                
                $this->SetTextColor(51, 51, 51);
                $this->setFont('', '', 6);
                $x=140;
                $heightAfterDetails=$this->MultiCellCountLines($x, 3, $tempString, 0, 'L')*3;
                $heightAfterDetails +=$lineheight+2;
                if(($this->GetY()+$heightAfterDetails)-280>=0){
                    $this->addPage();
                }
                $curY=$this->GetY();
                $x=0;
                $curX=10;
                
                $this->SetTextColor(0, 0, 0);
                $this->setFont('', '', 8);
                $this->setXY($curX+$x, $curY);
                $curX=$this->GetX();
                $x=9;            
                $this->MultiCell($x, $lineheight, $counter . '.', 0, 'L');
                
                $this->setXY($curX+$x, $curY);
                $curX=$this->GetX();
                $x=140;            
                $this->MultiCell($x, $lineheight, $recP->name, 0, 'L');
                
                $this->setXY($curX+$x, $curY);
                $curX=$this->GetX();
                $x=15;            
                $this->MultiCell($x, $lineheight, DEFAULTITEMUNIT, 0, 'C');
                
                $this->setXY($curX+$x+2, $curY);
                $curX=$this->GetX();
                $x=14;            
                $this->MultiCell($x, $lineheight, $qty, 0, 'R');
                
                $this->Ln(2);
                $curX=10;
                
                $this->SetTextColor(51, 51, 51);
                $this->setFont('', '', 6);
                $x=140;
                $this->SetXY($curX+9, $this->GetY()-2);
                $this->MultiCell($x, 3, $tempString, 0, 'L');
                $this->Ln(1);
                $curX=10;
                
                $this->Line($curX, $this->GetY(), 200, $this->GetY());
                $this->Ln(1);
            }
        }
        
        function wrapSingleRow($rec, $key, $product, $sumAllRabats, $counter, $curX=10){  
            $lineheight=4;
            $curY=$this->GetY();
            $x=0;
            $sum=$rec['sum'];
            $sumRabat=true===array_key_exists('rabatbyproduct', $rec)?$sum*($rec['rabatbyproduct']/100):$sum*($sumAllRabats/100);
            
            $sum=$sum-$sumRabat;
            $this->SetTextColor(0, 0, 0);
            $this->setFont('', '', 8);
            
            $recP=$product->getProductDetails($key);
            if($recP!==false && sizeof($recP)>0){
                
                
                $qty=$product->getQuantityFromSC($rec['data'], $recP->id);
                $singlePricePDV=$sum/$qty;
                //$singlePrice=$sum*(1-(PDV/100))/$qty;
                $singlePrice=($sum/(1+(PDV/100)))/$qty;
                $singlePriceFormatted=number_format($singlePrice, 2, ',', '.');
                
                $pdv=$singlePricePDV-$singlePrice;
                $pdvFormatted=number_format($pdv, 2, ',', '.');
                
                $sumNoPDV=$sum/(1+(PDV/100));
                $this->totalNoPDV +=$sumNoPDV;
                $this->totalPDV +=$sum-$sumNoPDV;
                //$this->totalPDV +=$sum*(1-(1/1+(PDV/100)));
                $sumNoPDVFormatted=number_format($sumNoPDV, 2, ',', '.');
                
                
                $tempString='';
                $tempCounter=0;
                foreach($rec['data'] as $val){
                    if($val['tblid']==19 && $val['value']=='Custom'){
                        if(true===array_key_exists('valuewidth', $val) && true===array_key_exists('valueheight', $val)){
                            $val['value']=$val['valuewidth'] . ' x ' . $val['valueheight'] . ' mm';
                        }
                    }
                    if($tempCounter>0) $tempString .="\n";
                    $tempString .=$val['title'] . ': ' . $val['value'];  
                    $tempCounter++;
                }
                
                $this->SetTextColor(51, 51, 51);
                $this->setFont('', '', 6);
                $x=66;
                $heightAfterDetails=$this->MultiCellCountLines($x, 3, $tempString, 0, 'L')*3;
                $heightAfterDetails +=$lineheight+2;
                if(($this->GetY()+$heightAfterDetails)-280>=0){
                    $this->addPage();
                }
                $curY=$this->GetY();
                $x=0;
                $curX=10;
                
                $this->SetTextColor(0, 0, 0);
                $this->setFont('', '', 8);
                $this->setXY($curX+$x, $curY);
                $curX=$this->GetX();
                $x=9;            
                $this->MultiCell($x, $lineheight, $counter . '.', 0, 'L');
                
                $this->setXY($curX+$x, $curY);
                $curX=$this->GetX();
                $x=66;            
                $this->MultiCell($x, $lineheight, $recP->name, 0, 'L');
                
                $this->setXY($curX+$x, $curY);
                $curX=$this->GetX();
                $x=20;            
                $this->MultiCell($x, $lineheight, DEFAULTITEMUNIT, 0, 'C');
                
                $this->setXY($curX+$x+2, $curY);
                $curX=$this->GetX();
                $x=15;            
                $this->MultiCell($x, $lineheight, $qty, 0, 'R');
                
                $this->setXY($curX+$x-2, $curY);
                $curX=$this->GetX();
                $x=25;            
                $this->MultiCell($x, $lineheight, $singlePriceFormatted . CURRENCY, 0, 'R');
                
                $this->setXY($curX+$x+4, $curY);
                $curX=$this->GetX();
                $x=18;            
                $this->MultiCell($x, $lineheight, $pdvFormatted . CURRENCY, 0, 'R');
                
                $this->setXY(($curX+$x+5), $curY);
                $curX=$this->GetX();
                $x=27;            
                $this->MultiCell($x, $lineheight, $sumNoPDVFormatted . CURRENCY, 0, 'R');
                $this->Ln(2);
                $curX=10;
                
                
                $this->SetTextColor(51, 51, 51);
                $this->setFont('', '', 6);
                $x=66;
                $this->SetXY($curX+9, $this->GetY()-2);
                $this->MultiCell($x, 3, $tempString, 0, 'L');
                $this->Ln(1);
                $curX=10;
                
                $this->Line($curX, $this->GetY(), 200, $this->GetY());
                $this->Ln(1);
            }
        }
        
        function createDisclaimer($isTemp=false, $curX=10){
            $this->SetTextColor(102, 102, 102);
            $this->setFont('', '', 4);
            $x=0;
            require_once 'HTMLContentFE.php';
            $htmlcontent=new HTMLContentFE();
            $rec=$htmlcontent->getPage(49);
            if($rec!==false && sizeof($rec)>0){
                $rec->content=strip_tags($rec->content);
                $str = str_replace(array("\n", '&nbsp;'), array(' ', ''), $rec->content);
                $str=html_entity_decode($str);
                $str=preg_replace('/[^a-zA-Z0-9ćčšđžČĆŽŠĐ\s\(\)]/', '', $str);
                $str=preg_replace('/\s+/', ' ', $str);
                $this->MultiCell(0, 2, $str);
                $this->Ln(1);
            }
        }
        
        function createTableSubTotals($isTemp=false, $curX=135){
            $this->SetTextColor(0, 0, 0);
            $this->setFont('', '', 8);
            $titleNoPDV='Ukupna vrijednost bez PDV-a';
            $titlePDV='Ukupno PDV-a po stopi ' . PDV . '%';
            $titleWithPDV='Ukupna vrijednost sa PDV-om';
            $this->Ln(20);
            if($this->GetY()-240>=0) $this->addPage();
            
            if($this->GetY()-40<=0){
                $this->SetY(-80);
            }
            $this->setX($curX);
            $lineheight=4;
            $this->Cell(20, $lineheight, $titleNoPDV, 0, 0, 'L');            
            $this->Cell(0, $lineheight, number_format($this->totalNoPDV, 2, ',', '.') . CURRENCY, 0, 1, 'R');
            
            $this->totalWidthPDV=$this->totalNoPDV+$this->totalPDV;    
            $this->setX($curX);
            $this->Cell(20, $lineheight, $titlePDV, 0, 0, 'L');            
            $this->Cell(0, $lineheight, number_format($this->totalPDV, 2, ',', '.') . 'KM', 0, 1, 'R');
            $this->Ln(1);
            $this->setX($curX);
            $this->Line($this->GetX(), $this->GetY(), 200, $this->GetY());
            $this->Ln(2);
            $this->SetFont('','B',8);
            $this->setX($curX);
            $this->Cell(20, $lineheight, $titleWithPDV, 0, 0, 'L');            
            $this->Cell(0, $lineheight, number_format($this->totalWidthPDV, 2, ',', '.') . 'KM', 0, 1, 'R');
            $this->Ln(10);
            $this->setX(10);
            $this->setFont('', 'B', 8);
            $this->Cell(22, $lineheight, 'Naziv narudžbe:', 0, 0, 'L');
            $this->setFont('', '', 8);
            $this->Cell(0, $lineheight, $this->rec->name, 0, 1, 'L');
            $this->setFont('', 'B', 8);
            $this->Cell(22, $lineheight, 'Napomena:', 0, 0, 'L');
            $this->setFont('', '', 8);
            $this->MultiCell(0, $lineheight, $this->rec->description, 0, 'L');
            $this->Ln(10);
        }
       
	function createTable1($isTemp=false){
		$this->createHeader($isTemp);
                $this->createTopHeaderInovice($isTemp);
                $this->createInoviceHeading($isTemp);
                $this->createTableInvoice($isTemp);
                $this->createTableSubTotals($isTemp);
	}
        
        function createTable2($isTemp=false){
		$this->createHeader($isTemp);
                $this->createTopHeaderWorkSheet($this->rec);
                $this->createWorksheetHeading($this->rec);
                $this->createTableInvoiceWorksheet($this->rec);
                /*
                
                
                $lineheight=4;
                $this->Ln(10);
                $this->setX(10);
                $this->setFont('', 'B', 8);
                $this->Cell(22, $lineheight, 'Naziv narudžbe:', 0, 0, 'L');
                $this->setFont('', '', 8);
                $this->Cell(0, $lineheight, $this->rec->name, 0, 1, 'L');
                $this->setFont('', 'B', 8);
                $this->Cell(22, $lineheight, 'Napomena:', 0, 0, 'L');
                $this->setFont('', '', 8);
                $this->MultiCell(0, $lineheight, $this->rec->description, 0, 'L');
                $this->Ln(10);
                 * 
                 */
                /*
                $this->createTableSubTotals($isTemp);                
                 */
	}
	
	
	
	function url_exists($url) {
		if(@file_get_contents($url,0,NULL,0,1)){return 1;}else{ return 0;}
	}
	
	function Image($file,$x=null,$y=null,$w=0,$h=0,$type='',$link='', $isMask=false, $maskImg=0)
	{
		//Put an image on the page
		if(!isset($this->images[$file]))
		{
			//First use of image, get info
			if($type=='')
			{
				$pos=strrpos($file,'.');
				if(!$pos)
					$this->Error('Image file has no extension and no type was specified: '.$file);
				$type=substr($file,$pos+1);
			}
			$type=strtolower($type);
			$mqr=get_magic_quotes_runtime();
			if(get_magic_quotes_runtime())
			{
			    // Deactivate
			   set_magic_quotes_runtime(0);
			}
			
			if($type=='jpg' || $type=='jpeg')
				$info=$this->_parsejpg($file);
			elseif($type=='png'){
				$info=$this->_parsepng($file);
				if ($info=='alpha') return $this->ImagePngWithAlpha($file,$x,$y,$w,$h,$link);
			}
			else
			{
				//Allow for additional formats
				$mtd='_parse'.$type;
				if(!method_exists($this,$mtd))
					$this->Error('Unsupported image type: '.$type);
				$info=$this->$mtd($file);
			}
			if(get_magic_quotes_runtime())
			{
			    // Deactivate
			  set_magic_quotes_runtime($mqr);
			}
			
			
			if ($isMask){
	      $info['cs']="DeviceGray"; // try to force grayscale (instead of indexed)
	    }
			$info['i']=count($this->images)+1;
			if ($maskImg>0) $info['masked'] = $maskImg;###
			$this->images[$file]=$info;
		}
		else
			$info=$this->images[$file];
		//Automatic width and height calculation if needed
		if($w==0 && $h==0)
		{
			//Put image at 72 dpi
			$w=$info['w']/$this->k;
			$h=$info['h']/$this->k;
		}
		if($w==0)
			$w=$h*$info['w']/$info['h'];
		if($h==0)
			$h=$w*$info['h']/$info['w'];
		
		// embed hidden, ouside the canvas
		if ((float)tFPDF_VERSION>=1.7){
			if ($isMask) $x = ($this->CurOrientation=='P'?$this->CurPageSize[0]:$this->CurPageSize[1]) + 10;
		}else{
			if ($isMask) $x = ($this->CurOrientation=='P'?$this->CurPageFormat[0]:$this->CurPageFormat[1]) + 10;
		}
		
		$this->_out(sprintf('q %.2f 0 0 %.2f %.2f %.2f cm /I%d Do Q',$w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));
		if($link)
			$this->Link($x,$y,$w,$h,$link);
			
		return $info['i'];
	}
	
	// needs GD 2.x extension
	// pixel-wise operation, not very fast
	function ImagePngWithAlpha($file,$x,$y,$w=0,$h=0,$link='')
	{
		$tmp_alpha = tempnam('.', 'mska');
		$this->tmpFiles[] = $tmp_alpha;
		$tmp_plain = tempnam('.', 'mskp');
		$this->tmpFiles[] = $tmp_plain;
		
		list($wpx, $hpx) = getimagesize($file);
		$img = imagecreatefrompng($file);
		$alpha_img = imagecreate( $wpx, $hpx );
		
		// generate gray scale pallete
		for($c=0;$c<256;$c++) ImageColorAllocate($alpha_img, $c, $c, $c);
		
		// extract alpha channel
		$xpx=0;
		while ($xpx<$wpx){
			$ypx = 0;
			while ($ypx<$hpx){
				$color_index = imagecolorat($img, $xpx, $ypx);
				$alpha = 255-($color_index>>24)*255/127; // GD alpha component: 7 bit only, 0..127!
				imagesetpixel($alpha_img, $xpx, $ypx, $alpha);
		    ++$ypx;
			}
			++$xpx;
		}
	
		imagepng($alpha_img, $tmp_alpha);
		imagedestroy($alpha_img);
		
		// extract image without alpha channel
		$plain_img = imagecreatetruecolor ( $wpx, $hpx );
		imagecopy ($plain_img, $img, 0, 0, 0, 0, $wpx, $hpx );
		imagepng($plain_img, $tmp_plain);
		imagedestroy($plain_img);
		
		//first embed mask image (w, h, x, will be ignored)
		$maskImg = $this->Image($tmp_alpha, 0,0,0,0, 'PNG', '', true); 
		
		//embed image, masked with previously embedded mask
		$this->Image($tmp_plain,$x,$y,$w,$h,'PNG',$link, false, $maskImg);
	}
	
	function Close()
	{
		parent::Close();
		// clean up tmp files
		foreach($this->tmpFiles as $tmp) @unlink($tmp);
	}
	
	/*******************************************************************************
	*                                                                              *
	*                               Private methods                                *
	*                                                                              *
	*******************************************************************************/
	function _putimages()
	{
		$filter=($this->compress) ? '/Filter /FlateDecode ' : '';
		reset($this->images);
		while(list($file,$info)=each($this->images))
		{
			$this->_newobj();
			$this->images[$file]['n']=$this->n;
			$this->_out('<</Type /XObject');
			$this->_out('/Subtype /Image');
			$this->_out('/Width '.$info['w']);
			$this->_out('/Height '.$info['h']);
			
			if (isset($info["masked"])) $this->_out('/SMask '.($this->n-1).' 0 R'); ###
			
			if($info['cs']=='Indexed')
				$this->_out('/ColorSpace [/Indexed /DeviceRGB '.(strlen($info['pal'])/3-1).' '.($this->n+1).' 0 R]');
			else
			{
				$this->_out('/ColorSpace /'.$info['cs']);
				if($info['cs']=='DeviceCMYK')
					$this->_out('/Decode [1 0 1 0 1 0 1 0]');
			}
			$this->_out('/BitsPerComponent '.$info['bpc']);
			if(isset($info['f']))
				$this->_out('/Filter /'.$info['f']);
			if(isset($info['parms']))
				$this->_out($info['parms']);
			if(isset($info['trns']) && is_array($info['trns']))
			{
				$trns='';
				for($i=0;$i<count($info['trns']);$i++)
					$trns.=$info['trns'][$i].' '.$info['trns'][$i].' ';
				$this->_out('/Mask ['.$trns.']');
			}
			$this->_out('/Length '.strlen($info['data']).'>>');
			$this->_putstream($info['data']);
			unset($this->images[$file]['data']);
			$this->_out('endobj');
			//Palette
			if($info['cs']=='Indexed')
			{
				$this->_newobj();
				$pal=($this->compress) ? gzcompress($info['pal']) : $info['pal'];
				$this->_out('<<'.$filter.'/Length '.strlen($pal).'>>');
				$this->_putstream($pal);
				$this->_out('endobj');
			}
		}
	}
	
	// this method overwriing the original version is only needed to make the Image method support PNGs with alpha channels.
	// if you only use the ImagePngWithAlpha method for such PNGs, you can remove it from this script.
	function _parsepng($file)
	{
		//Extract info from a PNG file
		$f=fopen($file,'rb');
		if(!$f)
			$this->Error('Can\'t open image file: '.$file);
		//Check signature
		if(fread($f,8)!=chr(137).'PNG'.chr(13).chr(10).chr(26).chr(10))
			$this->Error('Not a PNG file: '.$file);
		//Read header chunk
		fread($f,4);
		if(fread($f,4)!='IHDR')
			$this->Error('Incorrect PNG file: '.$file);
		$w=$this->_readint($f);
		$h=$this->_readint($f);
		$bpc=ord(fread($f,1));
		if($bpc>8)
			$this->Error('16-bit depth not supported: '.$file);
		$ct=ord(fread($f,1));
		if($ct==0)
			$colspace='DeviceGray';
		elseif($ct==2)
			$colspace='DeviceRGB';
		elseif($ct==3)
			$colspace='Indexed';
		else {
			fclose($f);      // the only changes are 
			return 'alpha';  // made in those 2 lines
		}
		if(ord(fread($f,1))!=0)
			$this->Error('Unknown compression method: '.$file);
		if(ord(fread($f,1))!=0)
			$this->Error('Unknown filter method: '.$file);
		if(ord(fread($f,1))!=0)
			$this->Error('Interlacing not supported: '.$file);
		fread($f,4);
		$parms='/DecodeParms <</Predictor 15 /Colors '.($ct==2 ? 3 : 1).' /BitsPerComponent '.$bpc.' /Columns '.$w.'>>';
		//Scan chunks looking for palette, transparency and image data
		$pal='';
		$trns='';
		$data='';
		do
		{
			$n=$this->_readint($f);
			$type=fread($f,4);
			if($type=='PLTE')
			{
				//Read palette
				$pal=fread($f,$n);
				fread($f,4);
			}
			elseif($type=='tRNS')
			{
				//Read transparency info
				$t=fread($f,$n);
				if($ct==0)
					$trns=array(ord(substr($t,1,1)));
				elseif($ct==2)
					$trns=array(ord(substr($t,1,1)),ord(substr($t,3,1)),ord(substr($t,5,1)));
				else
				{
					$pos=strpos($t,chr(0));
					if($pos!==false)
						$trns=array($pos);
				}
				fread($f,4);
			}
			elseif($type=='IDAT')
			{
				//Read image data block
				$data.=fread($f,$n);
				fread($f,4);
			}
			elseif($type=='IEND')
				break;
			else
				fread($f,$n+4);
		}
		while($n);
		if($colspace=='Indexed' && empty($pal))
			$this->Error('Missing palette in '.$file);
		fclose($f);
		return array('w'=>$w,'h'=>$h,'cs'=>$colspace,'bpc'=>$bpc,'f'=>'FlateDecode','parms'=>$parms,'pal'=>$pal,'trns'=>$trns,'data'=>$data);
	}
	
	function tFPDF_Protection($orientation='P', $unit='mm', $format='A4')
    {
        parent::tFPDF($orientation, $unit, $format);

        $this->encrypted=false;
        $this->last_rc4_key='';
        $this->padding="\x28\xBF\x4E\x5E\x4E\x75\x8A\x41\x64\x00\x4E\x56\xFF\xFA\x01\x08".
                        "\x2E\x2E\x00\xB6\xD0\x68\x3E\x80\x2F\x0C\xA9\xFE\x64\x53\x69\x7A";
    }

    /**
    * Function to set permissions as well as user and owner passwords
    *
    * - permissions is an array with values taken from the following list:
    *   copy, print, modify, annot-forms
    *   If a value is present it means that the permission is granted
    * - If a user password is set, user will be prompted before document is opened
    * - If an owner password is set, document can be opened in privilege mode with no
    *   restriction if that password is entered
    */
    function SetProtection($permissions=array(), $user_pass='', $owner_pass=null)
    {
        $options = array('print' => 4, 'modify' => 8, 'copy' => 16, 'annot-forms' => 32 );
        $protection = 192;
        foreach($permissions as $permission){
            if (!isset($options[$permission]))
                $this->Error('Incorrect permission: '.$permission);
            $protection += $options[$permission];
        }
        if ($owner_pass === null)
            $owner_pass = uniqid(rand());
        $this->encrypted = true;
        $this->_generateencryptionkey($user_pass, $owner_pass, $protection);
    }

/****************************************************************************
*                                                                           *
*                              Private methods                              *
*                                                                           *
****************************************************************************/

    function _putstream($s)
    {
        if ($this->encrypted) {
            $s = $this->_RC4($this->_objectkey($this->n), $s);
        }
        parent::_putstream($s);
    }

    function _textstring($s)
    {
        if ($this->encrypted) {
            $s = $this->_RC4($this->_objectkey($this->n), $s);
        }
        return parent::_textstring($s);
    }

    /**
    * Compute key depending on object number where the encrypted data is stored
    */
    function _objectkey($n)
    {
        return substr($this->_md5_16($this->encryption_key.pack('VXxx', $n)), 0, 10);
    }

    /**
    * Escape special characters
    */
    function _escape($s)
    {
        $s=str_replace('\\', '\\\\', $s);
        $s=str_replace(')', '\\)', $s);
        $s=str_replace('(', '\\(', $s);
        $s=str_replace("\r", '\\r', $s);
        return $s;
    }

    function _putresources()
    {
        parent::_putresources();
        if ($this->encrypted) {
            $this->_newobj();
            $this->enc_obj_id = $this->n;
            $this->_out('<<');
            $this->_putencryption();
            $this->_out('>>');
            $this->_out('endobj');
        }
    }

    function _putencryption()
    {
        $this->_out('/Filter /Standard');
        $this->_out('/V 1');
        $this->_out('/R 2');
        $this->_out('/O ('.$this->_escape($this->Ovalue).')');
        $this->_out('/U ('.$this->_escape($this->Uvalue).')');
        $this->_out('/P '.$this->Pvalue);
    }

    function _puttrailer()
    {
        parent::_puttrailer();
        if ($this->encrypted) {
            $this->_out('/Encrypt '.$this->enc_obj_id.' 0 R');
            $this->_out('/ID [()()]');
        }
    }

    /**
    * RC4 is the standard encryption algorithm used in PDF format
    */
    function _RC4($key, $text)
    {
        if ($this->last_rc4_key != $key) {
            $k = str_repeat($key, 256/strlen($key)+1);
            $rc4 = range(0, 255);
            $j = 0;
            for ($i=0; $i<256; $i++){
                $t = $rc4[$i];
                $j = ($j + $t + ord($k{$i})) % 256;
                $rc4[$i] = $rc4[$j];
                $rc4[$j] = $t;
            }
            $this->last_rc4_key = $key;
            $this->last_rc4_key_c = $rc4;
        } else {
            $rc4 = $this->last_rc4_key_c;
        }

        $len = strlen($text);
        $a = 0;
        $b = 0;
        $out = '';
        for ($i=0; $i<$len; $i++){
            $a = ($a+1)%256;
            $t= $rc4[$a];
            $b = ($b+$t)%256;
            $rc4[$a] = $rc4[$b];
            $rc4[$b] = $t;
            $k = $rc4[($rc4[$a]+$rc4[$b])%256];
            $out.=chr(ord($text{$i}) ^ $k);
        }

        return $out;
    }

    /**
    * Get MD5 as binary string
    */
    function _md5_16($string)
    {
        return pack('H*', md5($string));
    }

    /**
    * Compute O value
    */
    function _Ovalue($user_pass, $owner_pass)
    {
        $tmp = $this->_md5_16($owner_pass);
        $owner_RC4_key = substr($tmp, 0, 5);
        return $this->_RC4($owner_RC4_key, $user_pass);
    }

    /**
    * Compute U value
    */
    function _Uvalue()
    {
        return $this->_RC4($this->encryption_key, $this->padding);
    }

    /**
    * Compute encryption key
    */
    function _generateencryptionkey($user_pass, $owner_pass, $protection)
    {
        // Pad passwords
        $user_pass = substr($user_pass.$this->padding, 0, 32);
        $owner_pass = substr($owner_pass.$this->padding, 0, 32);
        // Compute O value
        $this->Ovalue = $this->_Ovalue($user_pass, $owner_pass);
        // Compute encyption key
        $tmp = $this->_md5_16($user_pass.$this->Ovalue.chr($protection)."\xFF\xFF\xFF");
        $this->encryption_key = substr($tmp, 0, 5);
        // Compute U value
        $this->Uvalue = $this->_Uvalue();
        // Compute P value
        $this->Pvalue = -(($protection^255)+1);
    }
    
    function curPageURL() {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"] == "on") {
            $pageURL .= "s";
        }
        $pageURL .= "://";
        if ($_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }
    
    function PXToMM($a, $dpi=96){
        return $a*25.4/$dpi;
    }
	
}
?>