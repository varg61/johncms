<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */
/**
 * @packege     Class Upload File
 * @autor       Krite
 * @version     1.0
 * @link        http://johncms.com
 */
defined( '_IN_JOHNCMS' ) or die( 'Error: restricted access' );
defined( '_IN_JOHNCMS_MAIL' ) or die( 'Error: restricted access' );

class Upload
{

    public $DIR;
    public $ERROR_EMPTY = false;
    public $FILE_TYPE;
    public $MAX_LEN;
    public $NEW_NAME_FILE;
    public $ALLOWED_TYPE = array(
        'zip',
        'rar',
        '7z',
        'tar',
        'gz',
        'mp3',
        'amr',
        'txt',
        'pdf',
        'doc',
        'rtf',
        'djvu',
        'xls',
        'jar',
        'jad',
        'jpg',
        'jpeg',
        'gif',
        'png',
        'bmp',
        'sis',
        'sisx',
        '3gp',
        'avi',
        'flv',
        'mpeg',
        'mp4',
        'exe',
        'msi' );
    public $MAX_FILE_SIZE = 1024;
    public $INFO;
    public $PREFIX_FILE;
    public $FILE_UPLOAD;

    private $MULTI;
    private $countFile;
    private $FILE_BODY;
    private $FILE_EXT;
    private $ERRORS;

    public function __construct( array $file = null )
    {

        $this->coutFiles = count( $file );
        if ( $this->coutFiles == 1 )
        {
            $this->INFO = $file[0];
        } else
        {
            $this->MULTI = $file;
        }
    }

    public function upload()
    {
        if ( $this->INFO['error'] == 4 && $this->ERROR_EMPTY == false )
        {
            return false;
        } else
        {
            if ( empty( $this->ERRORS ) )
            {
                if ( empty( $this->MULTI ) )
                {
                    return $this->uploadFile( $this->INFO['name'] );
                } else
                {
                    return false;
                }
            }
        }
        return false;
    }

    private function uploadFile( $file )
    {
        if ( $this->fileChecked( $file ) )
        {
            if ( $this->PREFIX_FILE == true )
            {
                $this->prefixFile();
            }
            if ( $this->rmkdir( rtrim( $this->DIR, '/\\' ), 0777 ) )
            {
                if ( move_uploaded_file( $this->INFO['tmp_name'], str_replace( array( '\\', '//' ), DIRECTORY_SEPARATOR,
                    rtrim( $this->DIR, '/\\' ) ) . DIRECTORY_SEPARATOR . $this->FILE_UPLOAD ) == true )
                {
                    if ( chmod( str_replace( array( '\\', '//' ), DIRECTORY_SEPARATOR, rtrim( $this->
                        DIR, '/\\' ) ) . DIRECTORY_SEPARATOR . $this->FILE_UPLOAD, 666 ) )
                    {
                        $this->clean( $this->INFO['tmp_name'] );
                        return true;
                    } else
                    {
                        return true;
                    }
                } else
                {
                    return false;
                }
            } else
            {
                return false;
            }
        }
        return false;
    }

    private function _mkdir( $path, $mode = 0777 )
    {
        $old = umask( 0 );
        $res = @mkdir( $path, $mode );
        umask( $old );
        return $res;
    }

    private function rmkdir( $path, $mode = 0777 )
    {
        return is_dir( $path ) || ( $this->rmkdir( dirname( $path ), $mode ) && $this->_mkdir( $path,
            $mode ) );
    }

    private function prefixFile()
    {
        $i = 1;
        while ( file_exists( rtrim( $this->DIR, '/\\' ) . DIRECTORY_SEPARATOR . $this->FILE_UPLOAD ) )
        {
            $this->FILE_UPLOAD = $this->FILE_BODY . '(' . $i . ').' . $this->FILE_EXT;
            $i++;
        }
    }

    private function fileExt( $file )
    {
        return mb_strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
    }

    private function fileBody( $file )
    {
        return mb_substr( $file, 0, mb_strripos( $file, '.' ) );
    }

    public function fileMime( $file )
    {
        return $this->mimes( $this->fileExt( $file ) );
    }

    public function errors()
    {
        if ( $this->ERRORS )
        {
            return implode( '<br />', $this->ERRORS );
        } else
        {
            return false;
        }
    }

    private function fileChecked( $file )
    {
        $this->FILE_BODY = $this->translit( $this->fileBody( $file ) );
        $this->FILE_EXT = $this->fileExt( $file );

        if ( $this->INFO['error'] == 0 && !$this->FILE_EXT )
            $this->ERRORS[] = 'Запрещены файлы без расширения!';
        else
            if ( $this->INFO['error'] == 0 && !in_array( $this->FILE_EXT, $this->ALLOWED_TYPE ) )
                $this->ERRORS[] = 'Недопустимое расширение файла! К загрузке разрешены только файлы с расширениями: ' .
                    implode( ', ', $this->ALLOWED_TYPE );
        if ( $this->INFO['error'] == 0 && !$this->FILE_BODY )
            $this->ERRORS[] = 'Запрещены файлы без имени!';
        if ( $this->INFO['error'] == 0 && $this->MAX_LEN && ( strlen( $this->FILE_BODY ) > $this->MAX_LEN ) )
            $this->ERRORS[] = 'Название файла превышает максимальное количество символов!';
        if ( $this->INFO['error'] == 0 && ( $this->MAX_FILE_SIZE < $this->INFO['size'] ) )
            $this->ERRORS[] = 'Размер файла привышает максимально допустимый!';
        if ( $this->INFO['error'] == 1 )
            $this->ERRORS[] = 'Размер файла привышает максимально допустимый!';
        if ( $this->INFO['error'] == 2 )
            $this->ERRORS[] = 'Размер файла привышает максимально допустимый!';
        if ( $this->INFO['error'] == 3 )
            $this->ERRORS[] = 'Загружаемый файл был получен только частично!';
        if ( $this->INFO['error'] == 4 )
            $this->ERRORS[] = 'Файл не был загружен.';
        if ( $this->INFO['error'] == 6 )
            $this->ERRORS[] = 'Отсутствует временная папка!';
        if ( $this->INFO['error'] == 7 )
            $this->ERRORS[] = 'Не удалось записать файл на диск!';
        if ( $this->INFO['error'] == 8 )
            $this->ERRORS[] = 'PHP-расширение остановило загрузку файла!';
        if ( preg_match( '/[^a-z0-9()\.\-\_]/i', $this->FILE_BODY ) )
            $this->ERRORS[] = 'Запрещенные символы в названии';

        if ( empty( $this->ERRORS ) )
        {
            $this->FILE_UPLOAD = $this->FILE_BODY . '.' . $this->FILE_EXT;
            return true;
        } else
        {
            return false;
        }
    }

    private static function translit( $text )
    {
        $rus = array(
            "а",
            "б",
            "в",
            "г",
            "ґ",
            "д",
            "е",
            "ё",
            "ж",
            "з",
            "и",
            "й",
            "к",
            "л",
            "м",
            "н",
            "о",
            "п",
            "р",
            "с",
            "т",
            "у",
            "ф",
            "х",
            "ц",
            "ч",
            "ш",
            "щ",
            "ы",
            "э",
            "ю",
            "я",
            "ь",
            "ъ",
            "і",
            "ї",
            "є",
            "А",
            "Б",
            "В",
            "Г",
            "ґ",
            "Д",
            "Е",
            "Ё",
            "Ж",
            "З",
            "И",
            "Й",
            "К",
            "Л",
            "М",
            "Н",
            "О",
            "П",
            "Р",
            "С",
            "Т",
            "У",
            "Ф",
            "Х",
            "Ц",
            "Ч",
            "Ш",
            "Щ",
            "Ы",
            "Э",
            "Ю",
            "Я",
            "Ь",
            "Ъ",
            "І",
            "Ї",
            "Є",
            " " );
        $lat = array(
            "a",
            "b",
            "v",
            "g",
            "g",
            "d",
            "e",
            "e",
            "zh",
            "z",
            "i",
            "j",
            "k",
            "l",
            "m",
            "n",
            "o",
            "p",
            "r",
            "s",
            "t",
            "u",
            "f",
            "h",
            "c",
            "ch",
            "sh",
            "sh'",
            "y",
            "e",
            "yu",
            "ya",
            "_",
            "_",
            "i",
            "i",
            "e",
            "A",
            "B",
            "V",
            "G",
            "G",
            "D",
            "E",
            "E",
            "ZH",
            "Z",
            "I",
            "J",
            "K",
            "L",
            "M",
            "N",
            "O",
            "P",
            "R",
            "S",
            "T",
            "U",
            "F",
            "H",
            "C",
            "CH",
            "SH",
            "SH'",
            "Y",
            "E",
            "YU",
            "YA",
            "_",
            "_",
            "I",
            "I",
            "E",
            "_" );
        $text = str_replace( $rus, $lat, $text );
        return preg_replace( '/[^a-z0-9()\.\-\_]/i', '', $text );
    }

    private function mimes( $ext = '' )
    {
        $array = array(
            '323' => array( 'text/h323' ),
            '7z' => array( 'application/x-7z-compressed' ),
            'abw' => array( 'application/x-abiword' ),
            'acx' => array( 'application/internet-property-stream' ),
            'ai' => array( 'application/postscript' ),
            'aif' => array( 'audio/x-aiff' ),
            'aifc' => array( 'audio/x-aiff' ),
            'aiff' => array( 'audio/x-aiff' ),
            'asf' => array( 'video/x-ms-asf' ),
            'asr' => array( 'video/x-ms-asf' ),
            'asx' => array( 'video/x-ms-asf' ),
            'atom' => array( 'application/atom+xml' ),
            'avi' => array(
                'video/avi',
                'video/msvideo',
                'video/x-msvideo' ),
            'bin' => array( 'application/octet-stream', 'application/macbinary' ),
            'bmp' => array( 'image/bmp' ),
            'c' => array( 'text/x-csrc' ),
            'c++' => array( 'text/x-c++src' ),
            'cab' => array( 'application/x-cab' ),
            'cc' => array( 'text/x-c++src' ),
            'cda' => array( 'application/x-cdf' ),
            'class' => array( 'application/octet-stream' ),
            'cpp' => array( 'text/x-c++src' ),
            'cpt' => array( 'application/mac-compactpro' ),
            'csh' => array( 'text/x-csh' ),
            'css' => array( 'text/css' ),
            'csv' => array(
                'text/x-comma-separated-values',
                'application/vnd.ms-excel',
                'text/comma-separated-values',
                'text/csv' ),
            'dbk' => array( 'application/docbook+xml' ),
            'dcr' => array( 'application/x-director' ),
            'deb' => array( 'application/x-debian-package' ),
            'diff' => array( 'text/x-diff' ),
            'dir' => array( 'application/x-director' ),
            'divx' => array( 'video/divx' ),
            'dll' => array( 'application/octet-stream', 'application/x-msdos-program' ),
            'dmg' => array( 'application/x-apple-diskimage' ),
            'dms' => array( 'application/octet-stream' ),
            'doc' => array( 'application/msword' ),
            'docx' => array( 'application/vnd.openxmlformats-officedocument.wordprocessingml.document' ),
            'dvi' => array( 'application/x-dvi' ),
            'dxr' => array( 'application/x-director' ),
            'eml' => array( 'message/rfc822' ),
            'eps' => array( 'application/postscript' ),
            'evy' => array( 'application/envoy' ),
            'exe' => array( 'application/x-msdos-program', 'application/octet-stream' ),
            'fla' => array( 'application/octet-stream' ),
            'flac' => array( 'application/x-flac' ),
            'flc' => array( 'video/flc' ),
            'fli' => array( 'video/fli' ),
            'flv' => array( 'video/x-flv' ),
            'gif' => array( 'image/gif' ),
            'gtar' => array( 'application/x-gtar' ),
            'gz' => array( 'application/x-gzip' ),
            'h' => array( 'text/x-chdr' ),
            'h++' => array( 'text/x-c++hdr' ),
            'hh' => array( 'text/x-c++hdr' ),
            'hpp' => array( 'text/x-c++hdr' ),
            'hqx' => array( 'application/mac-binhex40' ),
            'hs' => array( 'text/x-haskell' ),
            'htm' => array( 'text/html' ),
            'html' => array( 'text/html' ),
            'ico' => array( 'image/x-icon' ),
            'ics' => array( 'text/calendar' ),
            'iii' => array( 'application/x-iphone' ),
            'ins' => array( 'application/x-internet-signup' ),
            'iso' => array( 'application/x-iso9660-image' ),
            'isp' => array( 'application/x-internet-signup' ),
            'jar' => array( 'application/java-archive' ),
            'java' => array( 'application/x-java-applet' ),
            'jpe' => array( 'image/jpeg', 'image/pjpeg' ),
            'jpeg' => array( 'image/jpeg', 'image/pjpeg' ),
            'jpg' => array( 'image/jpeg', 'image/pjpeg' ),
            'js' => array( 'application/x-javascript' ),
            'json' => array( 'application/json' ),
            'latex' => array( 'application/x-latex' ),
            'lha' => array( 'application/octet-stream' ),
            'log' => array( 'text/plain', 'text/x-log' ),
            'lzh' => array( 'application/octet-stream' ),
            'm4a' => array( 'audio/mpeg' ),
            'm4p' => array( 'video/mp4v-es' ),
            'm4v' => array( 'video/mp4' ),
            'man' => array( 'application/x-troff-man' ),
            'mdb' => array( 'application/x-msaccess' ),
            'midi' => array( 'audio/midi' ),
            'mid' => array( 'audio/midi' ),
            'mif' => array( 'application/vnd.mif' ),
            'mka' => array( 'audio/x-matroska' ),
            'mkv' => array( 'video/x-matroska' ),
            'mov' => array( 'video/quicktime' ),
            'movie' => array( 'video/x-sgi-movie' ),
            'mp2' => array( 'audio/mpeg' ),
            'mp3' => array( 'audio/mpeg' ),
            'mp4' => array(
                'application/mp4',
                'audio/mp4',
                'video/mp4' ),
            'mpa' => array( 'video/mpeg' ),
            'mpe' => array( 'video/mpeg' ),
            'mpeg' => array( 'video/mpeg' ),
            'mpg' => array( 'video/mpeg' ),
            'mpg4' => array( 'video/mp4' ),
            'mpga' => array( 'audio/mpeg' ),
            'mpp' => array( 'application/vnd.ms-project' ),
            'mpv' => array( 'video/x-matroska' ),
            'mpv2' => array( 'video/mpeg' ),
            'ms' => array( 'application/x-troff-ms' ),
            'msg' => array( 'application/msoutlook', 'application/x-msg' ),
            'msi' => array( 'application/x-msi' ),
            'nws' => array( 'message/rfc822' ),
            'oda' => array( 'application/oda' ),
            'odb' => array( 'application/vnd.oasis.opendocument.database' ),
            'odc' => array( 'application/vnd.oasis.opendocument.chart' ),
            'odf' => array( 'application/vnd.oasis.opendocument.forumla' ),
            'odg' => array( 'application/vnd.oasis.opendocument.graphics' ),
            'odi' => array( 'application/vnd.oasis.opendocument.image' ),
            'odm' => array( 'application/vnd.oasis.opendocument.text-master' ),
            'odp' => array( 'application/vnd.oasis.opendocument.presentation' ),
            'ods' => array( 'application/vnd.oasis.opendocument.spreadsheet' ),
            'odt' => array( 'application/vnd.oasis.opendocument.text' ),
            'oga' => array( 'audio/ogg' ),
            'ogg' => array( 'application/ogg' ),
            'ogv' => array( 'video/ogg' ),
            'otg' => array( 'application/vnd.oasis.opendocument.graphics-template' ),
            'oth' => array( 'application/vnd.oasis.opendocument.web' ),
            'otp' => array( 'application/vnd.oasis.opendocument.presentation-template' ),
            'ots' => array( 'application/vnd.oasis.opendocument.spreadsheet-template' ),
            'ott' => array( 'application/vnd.oasis.opendocument.template' ),
            'p' => array( 'text/x-pascal' ),
            'pas' => array( 'text/x-pascal' ),
            'patch' => array( 'text/x-diff' ),
            'pbm' => array( 'image/x-portable-bitmap' ),
            'pdf' => array( 'application/pdf', 'application/x-download' ),
            'php' => array( 'application/x-httpd-php' ),
            'php3' => array( 'application/x-httpd-php' ),
            'php4' => array( 'application/x-httpd-php' ),
            'php5' => array( 'application/x-httpd-php' ),
            'phps' => array( 'application/x-httpd-php-source' ),
            'phtml' => array( 'application/x-httpd-php' ),
            'pl' => array( 'text/x-perl' ),
            'pm' => array( 'text/x-perl' ),
            'png' => array( 'image/png', 'image/x-png' ),
            'po' => array( 'text/x-gettext-translation' ),
            'pot' => array( 'application/vnd.ms-powerpoint' ),
            'pps' => array( 'application/vnd.ms-powerpoint' ),
            'ppt' => array( 'application/powerpoint' ),
            'pptx' => array( 'application/vnd.openxmlformats-officedocument.presentationml.presentation' ),
            'ps' => array( 'application/postscript' ),
            'psd' => array( 'application/x-photoshop', 'image/x-photoshop' ),
            'pub' => array( 'application/x-mspublisher' ),
            'py' => array( 'text/x-python' ),
            'qt' => array( 'video/quicktime' ),
            'ra' => array( 'audio/x-realaudio' ),
            'ram' => array( 'audio/x-realaudio', 'audio/x-pn-realaudio' ),
            'rar' => array( 'application/rar' ),
            'rgb' => array( 'image/x-rgb' ),
            'rm' => array( 'audio/x-pn-realaudio' ),
            'rpm' => array( 'audio/x-pn-realaudio-plugin', 'application/x-redhat-package-manager' ),
            'rss' => array( 'application/rss+xml' ),
            'rtf' => array( 'text/rtf' ),
            'rtx' => array( 'text/richtext' ),
            'rv' => array( 'video/vnd.rn-realvideo' ),
            'sea' => array( 'application/octet-stream' ),
            'sh' => array( 'text/x-sh' ),
            'shtml' => array( 'text/html' ),
            'sit' => array( 'application/x-stuffit' ),
            'smi' => array( 'application/smil' ),
            'smil' => array( 'application/smil' ),
            'so' => array( 'application/octet-stream' ),
            'src' => array( 'application/x-wais-source' ),
            'svg' => array( 'image/svg+xml' ),
            'swf' => array( 'application/x-shockwave-flash' ),
            't' => array( 'application/x-troff' ),
            'tar' => array( 'application/x-tar' ),
            'tcl' => array( 'text/x-tcl' ),
            'tex' => array( 'application/x-tex' ),
            'text' => array( 'text/plain' ),
            'texti' => array( 'application/x-texinfo' ),
            'textinfo' => array( 'application/x-texinfo' ),
            'tgz' => array( 'application/x-tar' ),
            'tif' => array( 'image/tiff' ),
            'tiff' => array( 'image/tiff' ),
            'torrent' => array( 'application/x-bittorrent' ),
            'tr' => array( 'application/x-troff' ),
            'tsv' => array( 'text/tab-separated-values' ),
            'txt' => array( 'text/plain' ),
            'wav' => array( 'audio/x-wav' ),
            'wax' => array( 'audio/x-ms-wax' ),
            'wbxml' => array( 'application/wbxml' ),
            'wm' => array( 'video/x-ms-wm' ),
            'wma' => array( 'audio/x-ms-wma' ),
            'wmd' => array( 'application/x-ms-wmd' ),
            'wmlc' => array( 'application/wmlc' ),
            'wmv' => array( 'video/x-ms-wmv', 'application/octet-stream' ),
            'wmx' => array( 'video/x-ms-wmx' ),
            'wmz' => array( 'application/x-ms-wmz' ),
            'word' => array( 'application/msword', 'application/octet-stream' ),
            'wp5' => array( 'application/wordperfect5.1' ),
            'wpd' => array( 'application/vnd.wordperfect' ),
            'wvx' => array( 'video/x-ms-wvx' ),
            'xbm' => array( 'image/x-xbitmap' ),
            'xcf' => array( 'image/xcf' ),
            'xhtml' => array( 'application/xhtml+xml' ),
            'xht' => array( 'application/xhtml+xml' ),
            'xl' => array( 'application/excel', 'application/vnd.ms-excel' ),
            'xla' => array( 'application/excel', 'application/vnd.ms-excel' ),
            'xlc' => array( 'application/excel', 'application/vnd.ms-excel' ),
            'xlm' => array( 'application/excel', 'application/vnd.ms-excel' ),
            'xls' => array( 'application/excel', 'application/vnd.ms-excel' ),
            'xlsx' => array( 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' ),
            'xlt' => array( 'application/excel', 'application/vnd.ms-excel' ),
            'xml' => array( 'text/xml', 'application/xml' ),
            'xof' => array( 'x-world/x-vrml' ),
            'xpm' => array( 'image/x-xpixmap' ),
            'xsl' => array( 'text/xml' ),
            'xvid' => array( 'video/x-xvid' ),
            'xwd' => array( 'image/x-xwindowdump' ),
            'z' => array( 'application/x-compress' ),
            'zip' => array(
                'application/x-zip',
                'application/zip',
                'application/x-zip-compressed' ) );
        return isset( $array[$ext] ) ? $array[$ext][0] : 'application/octet-stream';
    }

    private function clean( $file )
    {
        @unlink( $file );
    }
}
