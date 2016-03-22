<?php
/**
 * File name : NekoDownloader.php
 * Codename : Coffee & Vanilla
 * Begin : Saturday, ‎July ‎27, ‎2013, ‏‎8:49:20 PM
 * Description : Manga Download
 * Author : NeneNeko
 * (c) Copyright : nene.neko@msn.com
 */

class NekoDownloader
    {
 /** Variable
    */
    const VERSION = 'NekoDownloader v.2.9.9';
    public $MangaDirectory;
    public $MangaEpisodeDirectory;
    public $MangaName;
    public $MangaEpisode;
    public $MangaURL;
    public $CreateEpisodeDirectory = true;
    public $Move = null;
    public $Crop = null;
    public $Grayscale = false;
    private $DatabaseName = 'Libraries/MangaDB.xlsx';
    private $curlDataContent;
    public $curlDataMimetype;
    public $curlDataSize;
    public $MangaList;
    public $NameList;
    private $PageList;
    private $Downloaded;
    private $DownloadFail;
    private $DownloadReport = array();
    public $ErrorReport = array();
    public $Zip = false;
    public $Debug = array ();
    private $zipFile;

 /** __construct
    * Private constructor
    *
    * @return void
    */
    function __construct() {
        if (ob_get_level() == 0) 
            ob_start();
        $this->Database = new XLSXReader ( $this->DatabaseName );
        $this->load_setting();
        $this->load_import();
        return $this;
    }

    function __destruct ()
        {
        ob_end_flush();
        }

 /** load_setting
    * Private load_setting
    *
    * @return void
    */
    private function load_setting() {
        $TableDB = $this->Database->getSheetData ( 'Setting' );
        foreach ( $TableDB as $row )
            $this->Setting[$row[0]] = $row[1];
        return $this;
    }

 /** load_import
    * Private load_import
    *
    * @return void
    */
    private function load_import() {
        $TableDB = $this->Database->getSheetData ( 'Import' );
        foreach ( $TableDB as $Row )
            foreach ( $Row as $Column => $Value )
                $this->Import[$Row[0]][$TableDB[0][$Column]] = $Value ;
        return $this;
    }

    public function getfromFile ( $filename )
        {
        $Data = file ( $filename );
        $Data = array_filter ( $Data, 'strlen' );
        $this->MangaName = $Data[0];
        $Episodes = $this->toArray($Data[1]);
        $LinkEpisode = array();
        foreach ( $Episodes as $Episode )
            {
            if ( strpos ( $Episode, '-' ) )
                {
                $SpitEpisode = $this->toArray ( $Episode , '-' );
                for ( $i = $SpitEpisode['0'] ; $i < ( $SpitEpisode['1'] + '1' ) ; $i++ )
                    $LinkEpisode[] = $i;
                }
            else
                {
                $LinkEpisode[] = $Episode;
                }
            sleep(1);
            }
        unset($Data[0]);
        unset($Data[1]);
        $Data = array_values ( array_unique ( $Data ) );
        if( count ( $Data ) == count ( $LinkEpisode ) )
            {
            foreach ( $LinkEpisode as $id=>$Episode)
                {
                $this->MangaURL = $Data[$id];
                $this->MangaEpisode = $Episode;
                $this->Download ();
                }
            }
        else
            {
            echo count($Data ), '-', count ( $LinkEpisode );
            echo 'Episode not match';
            }
        }


 /** Download
    * Public Download
    *
    * @return void
    */
    public function Download ()
        {
        $this->MangaName = trim ( $this->MangaName );
        $this->MangaEpisode = trim ( $this->MangaEpisode );
        $this->MangaURL = trim ( $this->MangaURL );
        $this->CreateDirectory ( $this->Setting['download_directory'] );
        $this->CreateDirectory ( $this->Setting['database_directory'] );
        $this->CreateDirectory ( $this->Setting['cache_directory'] );
        $this->CreateDirectory ( $this->Setting['cookie_directory'] );

        if ( !isset ( $this->MangaEpisode ) or $this->MangaEpisode == '' )
            {
            $pos = strrpos($this->MangaName, ' ');
            $this->MangaEpisode = substr ( $this->MangaName, $pos + '1' );
            $this->MangaName = substr ($this->MangaName, 0, $pos);
            }
        $LinkEpisode = array();
        $EpisodeExplode = $this->toArray ( $this->MangaEpisode );
        foreach ( $EpisodeExplode as $Episode )
            {
            if ( strpos ( $Episode, '-' ) )
                {
                $SpitEpisode = $this->toArray ( $Episode , '-' );
                for ( $i = $SpitEpisode['0'] ; $i < ( $SpitEpisode['1'] + '1' ) ; $i++ )
                    $LinkEpisode[] = $i;
                }
            else
                {
                $LinkEpisode[] = $Episode;
                }
            sleep(1);
            }
        $urlPatturn = $this->MangaURL;

        /* Create xml database */
        $this->DatabaseFile = $this->Setting['database_directory'].'/'.$this->MangaName.'.xml';
        $this->Mangadb = new DOMDocument ( '1.0', 'UTF-8' );
        $this->Mangadb->preserveWhiteSpace = false;
        $this->Mangadb->formatOutput = true;
        if ( file_exists ( $this->DatabaseFile ) )
            {
            $this->Mangadb->load ( $this->DatabaseFile );
            }
        else
            {
            $MainDB = $this->Mangadb->createElement( 'NekoDB' );
            $MainDB->appendChild ( $this->Mangadb->createElement ( 'GeneratedBy', self::VERSION ) );
            $MainDB->appendChild ( $this->Mangadb->createElement ( 'CreateOn', date ( 'l jS \of F Y h:i:s A' ) ) );
            $Details = $this->Mangadb->createElement( 'Details' );
            $Details->appendChild ( $this->Mangadb->createElement ( 'Name', $this->MangaName ) );
            $Details->appendChild ( $this->Mangadb->createElement ( 'Description',  $this->MangaDetails ) );
            $UrlNode = $this->Mangadb->createElement ( 'URL' );
            $UrlNode->appendChild ( $this->Mangadb->createCDATASection( $this->MangaURL ) ); 
            $Details->appendChild ( $UrlNode );
            $Settings = $this->Mangadb->createElement ( 'Settings' );
            $Settings ->setAttribute( 'SaveFileType', $this->Setting['output_extension'] );
            $Details->appendChild( $Settings );
            $MainDB->appendChild( $Details );
            $this->Mangadb->appendChild( $MainDB );
            $this->WriteFile ( $this->DatabaseFile, $this->Mangadb->saveXML() );
            }
            $this->Chapterdb = new DOMXPath ( $this->Mangadb );
        if ( !isset ( $this->MangaURL ) or $this->MangaURL == '' )
            {
            $url = $this->Chapterdb->query ( '/NekoDB/Details/URL' );
            if ( $url->length )
                {
                if ( $url->item(0)->nodeType == XML_CDATA_SECTION_NODE )
                    $urlPatturn = $url->item(0)->textContent;
                else
                    $urlPatturn = $url->item(0)->nodeValue;
                }
            else
                {
                echo 'Can\'t detect manga url';
                }
            }

        echo 'Manga Name : '.$this->MangaName.'<br />';
        foreach ( array_values ( array_unique ( $LinkEpisode ) ) as $Episode )
            {
            $this->MangaList = array ();
            $this->ImportList = array();
            $this->PageList = array();
            $this->Downloaded = 0;
            $this->DownloadFail = 0;
            $this->DownloadReport = array();
            if ( preg_match ( '/\{EP([\+|\-|]\d+)\}/i', $urlPatturn, $Increase ) )
                $this->MangaURL = preg_replace ( '/\{EP.*?\}/i',  ( $Episode + ( int ) $Increase[1] ), $urlPatturn );
            else
                $this->MangaURL = str_replace ( '{EP}', $Episode, $urlPatturn );
            if ( $Episode == 'OneShot' )
                {
                $this->Setting['episode_prefix'] = false;
                $this->MangaEpisode = $Episode;
                }
            else
                {
                $this->MangaEpisode = $this->stringPad ( $Episode );
                }

            /* Set and create directory */
            $this->MangaDirectory = $this->Setting['download_directory'].$this->MangaName.'/';
            if ( $this->CreateEpisodeDirectory )
                $this->MangaEpisodeDirectory = $this->MangaDirectory.$this->MangaEpisode.'/';
            else
                $this->MangaEpisodeDirectory = $this->MangaDirectory.'/';
            $this->CreateDirectory ( $this->MangaDirectory );

            /* Start download */
            $this->zipFile = $this->MangaDirectory.$this->MangaName.' - '.$this->MangaEpisode.'.zip';
            if ( file_exists ( $this->zipFile ) )
                {
                $this->getMangaList ();
                echo '<span style="color:green">Already zip created : '.$this->MangaName.' - '.$this->MangaEpisode.'.zip</span><br />'."\n";
                $this->ViewZip ( $this->zipFile );
                }
            else
                {
                if ( $this->getMangaList () )
                    {
                    $this->Downloading ();
                    if ( $this->Zip ) $this->CreateZip ( $this->Zip );
                    $this->Report ();
                    }
                else
                    {
                    echo '-';
                    }
                }

            }
        }
        # End DownloadList

 /** getMangaList
    * Private getMangaList
    *
    * @return void
    */
    private function getMangaList ()
        {
        $getSettings = $this->Chapterdb->query ( '/NekoDB/Details/Settings' );
        if ( $getSettings->length > 0 )
            {
            $SaveFileType = $getSettings->item(0)->getAttribute ( 'SaveFileType' );
            if ( isset ( $SaveFileType ) )
                $this->Setting['output_extension'] = $SaveFileType;
            }
        $Images = $this->Chapterdb->query ( '/NekoDB/Chapter_'.$this->MangaEpisode.'/Images/Image' );
        $url = $this->Chapterdb->query ( '/NekoDB/Chapter_'.$this->MangaEpisode.'/URL' );
        if ( $Images->length )
            {
            $this->MangaURL = $url->item(0)->nodeValue;
            foreach ( $Images as $Image )
            if ( $Image->nodeType == XML_CDATA_SECTION_NODE)
                $this->MangaList[] = $Image->textContent;
            else
                $this->MangaList[] = $Image->nodeValue;
            }
        else
            {
            if ( isset ( $url->item(0)->nodeValue ) )
                {
                $this->MangaURL = $url->item(0)->nodeValue;
                $Chapter = $this->Mangadb->documentElement;
                $Chapter->removeChild ( $Chapter->getElementsByTagName ( 'Chapter_'.$this->MangaEpisode )->item(0));
                }
            if ( !$this->ExtractLink () ) return false;
            }
        $this->MangaList = $this->arrayChangeIndex ( $this->MangaList );
        return true;
        }
        #End getMangaList 

    private function ExtractLink ()
        {
        $NormalMatch = true;
        $subDomain = str_replace ( 'www.', '', parse_url ( $this->MangaURL, PHP_URL_HOST ) );
        $Domain = $this->GetDomain ( $this->MangaURL );
        if ( isset ( $this->Import[$Domain]['Urlfix'] ) )
            if ( substr ( $this->MangaURL, -strlen ( $this->Import[$Domain]['Urlfix'] ) ) != $this->Import[$Domain]['Urlfix'] )
                $this->MangaURL = $this->MangaURL.$this->Import[$Domain]['Urlfix'];
        $this->GetWithCurl ( $this->MangaURL, 'html' );

        if ( isset ( $this->Import[$Domain]['Scripts'] ) )
            eval ( $this->Import[$Domain]['Scripts'] );
        if ( isset ( $this->Importfile ) )
            eval ( file_get_contents ( $this->Importfile ) );
        //$this->curlDataContent = iconv ( 'UTF-16', 'utf-8//TRANSLIT', $this->curlDataContent);
        $this->Debug['DataContent'] = $this->curlDataContent;
        $MainDataContent = preg_replace ( $this->Setting['javascript_pattern'], '', $this->curlDataContent );
        libxml_use_internal_errors(TRUE);
        $MainDocument = new DOMDocument ();
        $MainDocument->recover = true;
        $MainDocument->loadHTML ( $MainDataContent );
        libxml_clear_errors ();
        $MainDomObject = new DOMXPath ( $MainDocument );

        if ( isset ( $this->Import[$subDomain]['Xpath'] ) )
            $Xpath = $this->Import[$subDomain]['Xpath'];
        elseif ( isset ( $this->Import[$Domain]['Xpath'] ) )
            $Xpath = $this->Import[$Domain]['Xpath'];
        else
            {
            if ( $NormalMatch )
                {
                $this->ErrorReport[] = 'Notice : ไม่มีข้อมูลของเว็ปไซต์ '.$Domain;
                return false;
                }
            }
        if ( isset ( $this->Import[$subDomain]['TitleXpath'] ) )
            $TitleXpath = $this->Import[$subDomain]['TitleXpath'];
        elseif ( isset ( $this->Import[$Domain]['TitleXpath'] ) )
            $TitleXpath = $this->Import[$Domain]['TitleXpath'];
        else
            $TitleXpath = null;

            /* get Title */
            $MangaTitle = '';
            if ( !isset ( $this->MangaTitle ) or $this->MangaTitle == '' )
                {
                if ( isset ( $TitleXpath ) )
                    {
                    $Title = $MainDomObject->Query ( $TitleXpath );
                    $MangaTitle = $Title->length ? $Title->item(0)->nodeValue : '';
                    }
                }
            elseif ( isset ( $this->MangaTitle ) )
                $MangaTitle = trim ( $this->MangaTitle );

        if ( $NormalMatch )
            {
            /* get Link */
            $Links = $MainDomObject->Query ( $Xpath );
            if ( !$Links->length ) return false;
            foreach ( $Links as $Link )
                {
                if ( isset( $this->Import[$Domain]['Attribute'] ) )
                    foreach ( $this->toArray ( $this->Import[$Domain]['Attribute'] ) as $Attribute )
                        if ( $Link->getAttribute ( $Attribute ) != null ){
                            $this->MangaList[] = $Link->getAttribute ( $Attribute );
                            file_put_contents('lii.txt', $Link->getAttribute ( $Attribute )."\r\n", FILE_APPEND);}
                elseif ( $Link->getAttribute ( 'src' ) != null ) 
                    $this->MangaList[] = $Link->getAttribute ( 'src' );
                elseif ( $Link->getAttribute ( 'file' ) != null )
                    $this->MangaList[] = $Link->getAttribute ( 'file' );
                elseif ( $Link->getAttribute ( 'href' ) != null )
                    $this->MangaList[] = $Link->getAttribute ( 'href' );
                elseif ( $Link->getAttribute ( 'data-src' ) != null )
                    $this->MangaList[] = $Link->getAttribute ( 'data-src' );
                elseif ( isset ( $this->ImportFormFile ) )
                    include ( $this->ImportFormFile );
                else
                    return false;
                }
            }

        if ( isset ( $this->Import[$Domain]['Match'], $this->Import[$Domain]['Replace'] ) )
            {
            $MangaList = array();
            foreach ( $this->MangaList as $Link )
                $MangaList[] = preg_replace ( $this->Import[$Domain]['Match'], $this->Import[$Domain]['Replace'], $Link );
            $this->MangaList = $MangaList;
            }
//        $MangaList = array();
//        foreach ( $this->MangaList as $Link )
//            {
//            if ( preg_match ( $this->Setting['url_pattern'], $Link, $matches ) );
//                $MangaList[] = trim ( $matches[0] );
//            }
//        $this->MangaList = $MangaList;

        $this->Debug['RawMangaList'][$this->MangaEpisode] = $this->MangaList;
        $this->Filterword ();
        $this->FilterImage ();
        if ( $this->Move ) $this->Order ( $this->Move );
        $this->MangaList = array_values ( array_unique ( $this->MangaList ) );
        $this->MangaList = $this->arrayChangeIndex ( $this->MangaList );
        $this->Debug['MangaList'][$this->MangaEpisode] = $this->MangaList;
        /* Save xml database */
        if ( count ( $this->MangaList ) )
            {
            $Chapter = $this->Mangadb->createElement( 'Chapter_'.$this->MangaEpisode );
            $UrlNode = $this->Mangadb->createElement ( 'URL' );
            $UrlNode->appendChild ( $this->Mangadb->createCDATASection( $this->MangaURL ) ); 
            $Chapter->appendChild ( $UrlNode );
            $Chapter->appendChild( $this->Mangadb->createElement( 'Title', $MangaTitle ) );
            $Chapter->appendChild( $this->Mangadb->createElement( 'Create', date ( 'l jS \of F Y h:i:s A' ) ) );
            $Images =  $this->Mangadb->createElement( 'Images' );
            foreach ( $this->MangaList as $id => $Image )
                {
                $ImageNode = $this->Mangadb->createElement ( 'Image' );
                $ImageNode ->setAttribute( 'id', $id );
                $ImageNode ->appendChild ( $this->Mangadb->createCDATASection( $Image ) ); 
                $Images->appendChild( $ImageNode );
                }
            $Chapter->appendChild( $Images );
            $this->Mangadb->documentElement->appendChild( $Chapter );
            $this->WriteFile ( $this->DatabaseFile, $this->Mangadb->saveXML() );
            }
        return true;
    }

 /** Filterword
    * Private Filterword
    * Remove ban keyword link
    * @return void
    */
    private function Filterword ()
        {
        $SkipPattern = '/' . join (
            '|', 
            array_map (
                function ( $Item ) { return preg_quote ( $Item, '/' ); }, 
                $this->toArray ( $this->Setting['skip_word'] ) )
            ) . '/i';
        foreach ( $this->MangaList as $Key => $Link ) 
            {
            if ( preg_match ( $SkipPattern, $Link ) )  unset ( $this->MangaList[$Key] );
            }
        $this->MangaList = array_values ( array_unique ( $this->MangaList ) );
        $this->Debug['Filterword'][$this->MangaEpisode] = $this->MangaList;
        }

 /** FilterImage
    * Private FilterImage
    *
    * @return void
    */
    private function FilterImage ()
        {
        $FilterLink = array();
        foreach ( $this->MangaList as $Link )
            {
            $Link = trim ( $Link ); 
            $ImageDomain = isset ( $Link ) ? $this->GetDomain ( $Link ) : '';
            if ( isset ( $this->Import[$ImageDomain]['LinkMatch'] ) )
                {
                strpos ( $Link, $this->Import[$ImageDomain]['LinkMatch'] ) ? $FilterLink[] =  $Link : '';
                }
            }
        $this->MangaList = $FilterLink;
        $this->Debug['FilterImage'][$this->MangaEpisode] = $FilterLink;
        }

 /** Order
    * public Order
    *
    * @return void
    */
    public function Order ( $move )
        {
        $end = count ( $this->MangaList ) - 1;
        foreach ( $this->toArray ( $move ) as $sort )
            {
            switch ( $sort )
                {
                case '1toend' :
                    $this->MangaList = $this->arrayOrder ( $this->MangaList , 0, $end );
                    break;
                case '1-2toend' :
                    $this->MangaList = $this->arrayOrder ( $this->MangaList , 0, $end );
                    $this->MangaList = $this->arrayOrder ( $this->MangaList , 0, $end );
                    break;
                case '2toend' :
                    $this->MangaList = $this->arrayOrder ( $this->MangaList , 1, $end );
                    break;
                case '3toend' :
                    $this->MangaList = $this->arrayOrder ( $this->MangaList , 2, $end );
                    break;
                case 'endto1' :
                    $this->MangaList = $this->arrayOrder ( $this->MangaList , $end, 0 );
                    break;
            default;
                    if ( !isset ( $sort ) ) break;
                    list ( $oldindex, $newindex ) = explode ( 'to', $sort );
                    if ( $oldindex == 'end' ) $oldindex = $end; else $oldindex--;
                    if ( $newindex == 'end' ) $newindex = $end; else $newindex--;
                    if ( $oldindex > $end or $newindex > $end ) break;
                    $this->MangaList = $this->arrayOrder ( $this->MangaList , $oldindex, $newindex );
                    break;
            }
        }
        $this->Debug['Reorder'][$this->MangaEpisode] = $this->MangaList;
        }

 /** Downloading
    * Private Downloading
    *
    * @return void
    */
    private function Downloading ()
        {
        $this->CreateDirectory ( $this->MangaEpisodeDirectory );
        foreach ( $this->MangaList  as $Page => $Link )
            {
            $Page = str_pad ( ( int ) $Page, $this->Setting['page_digit'], '0', STR_PAD_LEFT );
            if ( $this->Setting['episode_prefix'] )
                $Page = $this->MangaEpisode.'_'.$Page;
            $Pagename = $Page.'.'.$this->Setting['output_extension'];
            $this->PageList[] = $Pagename;
            $ImageFilename = $this->MangaEpisodeDirectory.$Pagename;
            if ( file_exists ( $ImageFilename ) )
                {
                $this->Downloaded++;
                $this->DownloadReport[] = $Pagename;
                }
            else
                {
                for ( $i=0 ; $i < $this->Setting['resume_download'] ; $i++ )
                    {
                    $this->GetWithCurl ( $Link, $this->Setting['output_extension'] );
                    if ( $this->curlDataContent != null ) break;
                    sleep(5);
                    }
                if( $this->curlDataContent != null && $this->Setting['output_extension'] == 'gif')
                    {
                    file_put_contents($ImageFilename, $this->curlDataContent);
                    }
                elseif( $this->curlDataContent != null && $this->Setting['recompassed'] == false)
                    {
                    file_put_contents($ImageFilename, $this->curlDataContent);
                    }
                elseif ( $this->curlDataContent != null && ($this->Setting['output_extension'] == 'png' || $this->Setting['output_extension'] == 'jpg' && $this->Setting['recompassed']))
                    {
                    $RawImage = imagecreatefromstring ( $this->curlDataContent );
                    $image = new PHPImage();
                    $image->setResource($RawImage);
                    if ( $image )
                        {
                        if ($this->Setting['output_extension'] == 'jpg')
                            $image->setOutput($this->Setting['output_extension'], $this->Setting['jpeg_quality']);
                       else
                            $image->setOutput($this->Setting['output_extension'], $this->Setting['png_compression']);
                        if ( isset ( $this->Crop ) )
                            {
                            if ( $this->Crop == 'trim' )
                                {
                                $image->Trim();
                                }
                            else
                                {
                                $crop = $this->toArray ( $this->Crop );
                                $image->crop($crop[0], $crop[1], $crop[2], $crop[3]);
                                }
                            }
                        if ( $this->Grayscale )
                            $image->applyFilter(IMG_FILTER_GRAYSCALE);
                        $image->save($ImageFilename, false, false);
                        if ( $this->Setting['png_optimizer'] )
                            exec ( '"'.$this->Setting['png_optimizer_filename'].'" -file:"'.$ImageFilename.'" -KeepBackgroundColor:R' );
                        $this->Downloaded++;
                        $this->DownloadReport[] = $Pagename;
                        }
                    else
                        {
                        $this->DownloadFail++;
                        $this->DownloadReport[] = 'error';
                        }
                    }
                else
                    {
                    $this->DownloadFail++;
                    $this->DownloadReport[] = 'error';
                    }
                }
            }
        }
        # End DownloadManga

 /** GetWithCurl
    * Private GetWithCurl
    *
    * @return bool
    */
    private function GetWithCurl ( $url , $cacheext = '' )
        {
        $Domain = $this->GetDomain ( $url );
        $Cachefile = $this->Setting['cache_directory'].hash ( 'crc32b', $url ).'.'.$cacheext;
        if ( $this->Setting['cache_download_file'] and file_exists ( $Cachefile ) )
            {
            $this->curlDataContent = file_get_contents ( $Cachefile );
            }
        else
            {
            $curl = curl_init ();
            $header[0] = "Accept: text/xml,application/xml,application/xhtml+xml,";
            $header[0] .= "text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
            $header[] = "Cache-Control: max-age=0";
            $header[] = "Connection: keep-alive";
            $header[] = "Keep-Alive: 300";
            $header[] = "Accept-Encoding: gzip,deflate";
            $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
            $header[] = "Accept-Language: en-us,en;q=0.5";
            $header[] = "Pragma: ";
            if ( isset ( $this->Import[$Domain]['SelfRefferent'] ) )
                $referer = "http://".$Domain."/";
            else
                $referer = $this->MangaURL;
            $Cookiefile = getcwd().'/'.$this->Setting['cookie_directory'].$Domain.'.txt';
            $CAfile = getcwd().'/'.$this->Setting['ca_cert_filename'];
            curl_setopt ( $curl, CURLOPT_URL, $url );
            if ( isset ( $this->Import[$Domain]['UserAgent'] ) )
                curl_setopt ( $curl, CURLOPT_USERAGENT, $this->Setting['browser_useragent'] );
            if ( isset ( $this->Import[$Domain]['httpHeader'] ) )
                curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header);
            curl_setopt ( $curl, CURLOPT_HEADER , false );
            if ( isset ( $this->Import[$Domain]['Refferent'] ) )
                curl_setopt ( $curl, CURLOPT_REFERER, $referer );
            curl_setopt ( $curl, CURLOPT_AUTOREFERER, true );
            curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
            curl_setopt ( $curl, CURLOPT_TIMEOUT, $this->Setting['curl_timeout'] );
            curl_setopt ( $curl, CURLOPT_MAXREDIRS, 7 );
            curl_setopt ( $curl, CURLOPT_FOLLOWLOCATION, true );
            curl_setopt ( $curl, CURLOPT_ENCODING, '' );
            //if ( file_exists ( $Cookiefile ) )
            //    {
                curl_setopt ( $curl, CURLOPT_COOKIEJAR, $Cookiefile );
                curl_setopt ( $curl, CURLOPT_COOKIE, "cookiename=0" );
                curl_setopt ( $curl, CURLOPT_COOKIEFILE, $Cookiefile );
            //    }
            if ( substr ( $url, 0, 5 ) == 'https' )
                {
                curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, true );
                curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 2 );
                curl_setopt ( $curl, CURLOPT_CAINFO, $CAfile );
                }
            $DataContent = curl_exec ( $curl );
            if ( $DataContent === false )
                {
                $this->curlDataContent = null;
                $this->ErrorReport[] = 'Notice : '.curl_error ( $curl );
                return false;
                }
            else
                {
                $ContentSize = curl_getinfo ( $curl, CURLINFO_SIZE_DOWNLOAD );
                if ( isset ( $this->Import[$Domain]['MD5skip'] ) 
                    and $this->Import[$Domain]['MD5skip'] == md5 ( $DataContent ) )
                    {
                    $this->curlDataContent = null;
                    $this->ErrorReport[] = 'Notice : '.$url.' ตรงกับไฟล์รูปที่กำหนดให้ข้ามไป';
                    }
                elseif ( $ContentSize <= $this->Setting['accept_bytes'] and $cacheext != 'html')
                    {
                    $this->curlDataContent = null;
                    $this->ErrorReport[] = 'Notice : '.$url.' มีจำนวนไบต์น้อยกว่าที่กำหนด '.$ContentSize.' ไปต์';
                    }
                else
                    {
                    $this->curlDataContent = $DataContent;
                    if ( $this->Setting['cache_download_file'] )
                        file_put_contents ( $Cachefile, $this->curlDataContent );
                    }
                }
            curl_close ( $curl );
            return true;
            }
        }

 /** Report
    * Private Report
    *
    * @return void
    */
    private function Report ()
        {
        if ( !$this->Zip == 'zip&delete' or $this->DownloadFail ) {
            echo 'Episode : '.$this->MangaEpisode.' - ( '.count ( $this->MangaList  ) .' Page )<br />'."\n";
            echo 'Downloaded  : '.$this->Downloaded;
            echo $this->DownloadFail ? ' Download Fail <span style="color:red">'.$this->DownloadFail.'</span><br />' : '<br />'."\n";
            foreach ( $this->DownloadReport as $Key => $Page )
                {
                echo '<span><a href="'.$this->MangaList[ ( $Key + 1 ) ].'" title="'.$this->stringPad ( $Key + 1 ).'">';
                echo '<img src="Images.php?img='.$this->MangaEpisodeDirectory.$Page.'&w='.$this->Setting['thumbnail_width'].'" border="0" /></a></span> '."\n";
                }
            if ( file_exists ( $this->zipFile ) )
                {
                echo '<br />Create zip : '.$this->MangaName.' - '.$this->MangaEpisode.'.zip<br />'."\n";
                }
            echo '<br />'."\n";
            }
        else
            {
            if ( file_exists ( $this->zipFile ) )
                {
                echo 'Create zip : '.$this->MangaName.' - '.$this->MangaEpisode.'.zip<br />'."\n";
                $this->ViewZip ( $this->zipFile );
                }
            }
        echo '<br />'."\n";
        ob_flush();
        flush();
        }

 /** ViewZip
    * Private ViewZip
    *
    * @param string $file
    * @return void
    */
    private function ViewZip ( $file )
        {
        $zip = new ZipArchive;
        $zip->open ( $file );
        echo 'Episode : '.$this->MangaEpisode.' ( '.$zip->numFiles.' Page )<br />'."\n";
        for ($i = 0; $i < $zip->numFiles; $i++)
            {
            echo '<a href="Images.php?img='.$zip->getNameIndex ( $i ).'&w=1000&zip='.$file.'" title="'.$this->stringPad ( $i + 1 ).'">';
            echo '<img src="Images.php?img='.$zip->getNameIndex ( $i ).'&w='.$this->Setting['thumbnail_width'].'&zip='.$file.'" border="0">';
            echo '</a>'."\n";
            }
        echo '<br /><br />'."\n";
        $zip->close ();
        }

 /** CreateZip
    * Private CreateZip
    *
    * @param string $Delete
    * @return void
    */
    private function CreateZip ( $Delete = false )
        {
        if ( $this->DownloadFail or !file_exists ( $this->DatabaseFile ) )
            {
            return 'Can\' create zip file. Because all download file not complete';
            }
        $zipData = new ZipArchive;
        $zipData->open ( $this->zipFile, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE );
        $zipData->setArchiveComment ( 'Created by '.self::VERSION ."\n".'Created on ' . date ( 'l jS \of F Y h:i:s A' )."\n".'Download from '.$this->MangaURL);
        //$zipData->addFile ( $this->DatabaseFile, $this->FileMetaData );
        foreach ( $this->PageList as $Page )
            {
            $zipData->addFile ( $this->MangaEpisodeDirectory.$Page, $Page );
            }
        $zipData->close();
        if ( $Delete == 'zip&delete' )
            {
            $this->DeleteDirectory ( $this->MangaEpisodeDirectory );
            }
        }

 /** ShowDebug
    * Private ShowDebug
    *
    * @return void
    */
    public function ShowDebug ()
        {
        echo '<div style="padding: 5px;margin-bottom: 10px;background-color: rgb(238, 238, 238);overflow: auto;width: auto;max-height: 700px;">';
        if (isset($this->Debug['DataContent']))
            {
            echo 'DataContent<br /><textarea name="" style="width: 1100px;height: 300px;">';
            echo htmlentities ( $this->Debug['DataContent'] );
            echo '</textarea><br /><br />';
            }
        if (isset($this->Debug['RawMangaList']))
            {
            echo '<div>RawMangaList</div><div style="overflow: auto;width: auto;max-height: 200px;">'."\n";
            $this->print_array ( $this->Debug['RawMangaList'] );
            echo '</div><br />'."\n";
            }
        if (isset($this->Debug['Filterword']))
            {
            echo '<div>Filterword</div><div style="overflow: auto;width: auto;max-height: 200px;">'."\n";
            $this->print_array ( $this->Debug['Filterword'] );
            echo '</div><br />'."\n";
            }
        if (isset($this->Debug['FilterImage']))
            {
            echo '<div>FilterImage</div><div style="overflow: auto;width: auto;max-height: 200px;">'."\n";
            $this->print_array ( $this->Debug['FilterImage'] );
            echo '</div><br />'."\n";
            }
        if (isset($this->Debug['MangaList']))
            {
            echo '<div>MangaList</div><div style="overflow: auto;width: auto;max-height: 200px;">'."\n";
            $this->print_array ( $this->Debug['MangaList'] );
            echo '</div><br />'."\n";
            }
        if (isset($this->ErrorReport))
            {
            echo '<div>ErrorReport</div><div style="overflow: auto;width: auto;max-height: 200px;">'."\n";
            foreach ( $this->ErrorReport as $Report )
                echo $Report.'<br />';
            echo '</div><br />'."\n";
            }
        echo '</div>';
        }

        function print_array ( $array )
        {
        foreach ( $array as $key=>$value )
            {
            echo '<div>[Chapter_'.$key.'] '."\n";
            foreach ( $value as $key2=>$value2 )
                echo '<a href="'.$value2.'">#'.$key2.'</a> '."\n";
            echo '</div>';
            }
        }

 /** getDomain
    * Private getDomain
    * extract Domain
    * @param string $url
    * @return void
    */
    private function getDomain ( $url )
        {
        $url = parse_url ( $url, PHP_URL_HOST );
        foreach ( $this->toArray ( $this->Setting['special_domains'] ) as $SpecialDomain )
            {
            $DomainExtension = substr ( $url, -strlen ( $SpecialDomain ) );
            if ( $DomainExtension == $SpecialDomain )
                {
                $DomainName = substr ( $url, 0, -strlen ( $SpecialDomain ) );
                $DomainName = str_replace ( '.', '', substr ( $DomainName, -strlen ( strrchr ( $DomainName, '.' ) ) ) );
                return $DomainName.$DomainExtension;
                }
            }
        $DomainExtension = strrchr ( $url, '.' );
        $DomainName = substr ( $url, 0, -strlen ( $DomainExtension ) );
        $DomainName = str_replace ( '.', '', substr ( $DomainName, -strlen ( strrchr ( $DomainName, '.' ) ) ) );
        return $DomainName.$DomainExtension;
        }

 /** CreateDirectory
    * Private CreateDirectory
    *
    * @param string $directory
    * @return bool
    */
    private function CreateDirectory ( $directory )
        {
        if ( isset ( $directory ) )
            {
            file_exists ( $directory ) ? '' : mkdir ( $directory, 0700 );
            return true;
            }
        else
            {
            return false;
            }
        }

 /** DeleteDirectory
    * Private DeleteDirectory
    *
    * @param string $directory
    * @return void
    */
    private function DeleteDirectory ( $directory )
        {
        if ( is_dir ( $directory ) )
            {
            $files = glob ( $directory . '*', GLOB_MARK );
            foreach ( $files as $file )
            $this->DeleteDirectory ( $file );
            rmdir( $directory );
            }
        else
            {
            unlink( $directory );
            }
        }

 /** toArray
    * Private toArray
    *
    * @param string $string
    * @param string $split
    * @return array
    */
    private function toArray ( $string, $split = ',' )
        {
        return array_filter ( array_map ( 'trim', explode ( $split, $string ) ), 'strlen' );
        }

 /** Report
    * Private Report
    *
    * @param string $string
    * @return string
    */
    private function stringPad ( $string )
        {
        return str_pad ( (int) $string, $this->Setting['page_digit'], '0', STR_PAD_LEFT ).strrchr ( $string, '.' );
        }

 /** arrayOrder
    * Private arrayOrder
    *
    * @param array $array
    * @param int $oldIndex
    * @param int $newIndex
    * @return array
    */
    private function arrayOrder ( $array, $oldIndex, $newIndex )
        {
        array_splice (
            $array,
            $newIndex,
            count ( $array ),
            array_merge (
                array_splice ( $array, $oldIndex, 1 ),
                array_slice ( $array, $newIndex, count ( $array ) )
            )
        );
        return $array;
        }

 /** arrayChangeIndex
    * Private arrayChangeIndex
    *
    * @param array $array
    * @return array
    */
    private function arrayChangeIndex ( $array )
        {
        return array_combine ( range( 1, count ( $array ) ), $array );
        }

 /** WriteFile
    * Write file
    *
    * @param srting $Filename
    * @param srting $Content
    * @return bool
    */
    private function WriteFile ( $Filename, $Content ) 
        {
        $Content = str_replace ( "\n","\r\n", $Content );
        if ( !$File = fopen ( $Filename, "w" ) )
            {
            return false;
            }
        //Add byte order mark UTF-8
        if ( fwrite ( $File, pack ( "CCC", 0xef, 0xbb, 0xbf ) ) === false )
            {
            return false;
            }
        fwrite ( $File, $Content ); 
        fclose ( $File );
        return true;
        }


 /** End class */
    }


?>