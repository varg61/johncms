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
//Закрываем прямой доступ к файлу
defined( '_IN_JOHNCMS_MAIL' ) or die( 'Error: restricted access' );
class UploadMail
{
    public $DIR;
    public $ERROR_EMPTY = false;
    public $FILE_TYPE;
    public $MAX_LEN;
    public $NEW_NAME_FILE;
    public $ALLOWED_TYPE = array(
        'zip',
		'apk',
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
	
	/**
	---------------------------------------------
	Конструктор класса
	---------------------------------------------
	*/
    public function __construct( array $file = null )
    {
        //Получаем и проверяем один ли файл грузится
		$this->coutFiles = count( $file );
        if ( $this->coutFiles == 1 )
        {
            $this->INFO = $file[0];
        } else
        {
            $this->MULTI = $file;
        }
    }
	
	/**
	---------------------------------------------
	Вспомогательная функция загрузки файла
	---------------------------------------------
	*/
    public function upload()
    {
        if ( $this->INFO['error'] == 4 && $this->ERROR_EMPTY == false )
        {
            return false;
        } else
        {
            if ( empty( $this->ERRORS ) )
            {
                //Проверя на мульти загрузку
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

	/**
	---------------------------------------------
	Функция загрузки файла
	---------------------------------------------
	*/
    private function uploadFile( $file )
    {
        if ( $this->fileChecked( $file ) )
        {
            if ( $this->PREFIX_FILE == true )
            {
                $this->prefixFile();
            }
            if ( $this->rmkdir( $this->DIR, 0777 ) )
            {
                if ( move_uploaded_file( $this->INFO['tmp_name'], $this->DIR . DIRECTORY_SEPARATOR . $this->FILE_UPLOAD ) === true )
                {
					//chmod( $this->DIR . DIRECTORY_SEPARATOR . $this->FILE_UPLOAD, 666 );
					unlink( $this->INFO['tmp_name'] );
					return true;
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

	/**
	---------------------------------------------
	Функция для рекурсивного создания дирректорий загрузки файла
	---------------------------------------------
	*/
    private function rmkdir( $path, $mode = 0777 )
    {
        return is_dir( $path ) || ( $this->rmkdir( dirname( $path ), $mode ) && $this->_mkdir( $path,
            $mode ) );
    }

	/**
	---------------------------------------------
	Подставляем префикс, если файл уже есть такой
	---------------------------------------------
	*/
    private function prefixFile()
    {
        $i = 1;
        while ( file_exists( rtrim( $this->DIR, '/\\' ) . DIRECTORY_SEPARATOR . $this->FILE_UPLOAD ) )
        {
            $this->FILE_UPLOAD = $this->FILE_BODY . '(' . $i . ').' . $this->FILE_EXT;
            $i++;
        }
    }

	/**
	-----------------------------------
	Получение расширения файла
	-----------------------------------
	*/
    private function fileExt( $file )
    {
        return mb_strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );
    }

	/**
	-----------------------------------
	Получение названия файла
	-----------------------------------
	*/
    private function fileBody( $file )
    {
        return mb_substr( $file, 0, mb_strripos( $file, '.' ) );
    }
	
	/**
	-----------------------------------
	Проверка функция вывода ошибки
	-----------------------------------
	*/
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
	
	/**
	-----------------------------------
	Проверка валидности файла
	-----------------------------------
	*/
    private function fileChecked( $file )
    {
        //Получаем и отсекаем все не нужные символы в имени файла
		$this->FILE_BODY = $this->translit( $this->fileBody( $file ) );
        //Получаем расширение
		$this->FILE_EXT = $this->fileExt( $file );
		
		//Проверяем что файл с расширением
        if ( $this->INFO['error'] == 0 && !$this->FILE_EXT )
            $this->ERRORS[] = 'Запрещены файлы без расширения!';
        //Проверяем на допустимое расширение
		else if ( $this->INFO['error'] == 0 && !in_array( $this->FILE_EXT, $this->ALLOWED_TYPE ) )
                $this->ERRORS[] = 'Недопустимое расширение файла! К загрузке разрешены только файлы с расширениями: ' .
                    implode( ', ', $this->ALLOWED_TYPE );
        //Проверяем что файл имеет имя
		if ( $this->INFO['error'] == 0 && !$this->FILE_BODY )
            $this->ERRORS[] = 'Запрещены файлы без имени!';
        //Проверяем длинну файла
		if ( $this->INFO['error'] == 0 && $this->MAX_LEN && ( strlen( $this->FILE_BODY ) > $this->MAX_LEN ) )
            $this->ERRORS[] = 'Название файла превышает максимальное количество символов!';
        //Проверяем максимальный вес файла
		if ( $this->INFO['error'] == 0 && ( $this->MAX_FILE_SIZE < $this->INFO['size'] ) )
            $this->ERRORS[] = 'Размер файла превышает максимально допустимый!';
        //Проверяем серверные ошибки при загрузке
		if ( $this->INFO['error'] == 1 )
            $this->ERRORS[] = 'Размер файла превышает максимально допустимый!';
        if ( $this->INFO['error'] == 2 )
            $this->ERRORS[] = 'Размер файла превышает максимально допустимый!';
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
        //Проверяем на запрещенные символы в файле
		if ( preg_match( '/[^a-z0-9()\.\-\_]/i', $this->FILE_BODY ) )
            $this->ERRORS[] = 'Запрещенные символы в названии';

        if ( empty( $this->ERRORS ) )
        {
            //Если ошибкок нет
			$this->FILE_UPLOAD = $this->FILE_BODY . '.' . $this->FILE_EXT;
            return true;
        } else
        {
            return false;
        }
    }
	
	/**
	-----------------------------------
	Транслит
	-----------------------------------
	*/
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
	
	/*
    -----------------------------------------------------------------
    Форматируем размер файла
    -----------------------------------------------------------------
    */
    public static function formatsize( $var = 0 )
    {
        if ( $var >= 1073741824 )
            $var = round( $var / 1073741824 * 100 ) / 100 . ' Gb';
        elseif ( $var >= 1048576 )
            $var = round( $var / 1048576 * 100 ) / 100 . ' Mb';
        elseif ( $var >= 1024 )
            $var = round( $var / 1024 * 100 ) / 100 . ' Kb';
        else
            $var = $var . ' b';
        return $var;
    }
	
	/*
    -----------------------------------------------------------------
    Иконки к файлам
    -----------------------------------------------------------------
    */
    public static function fileicon( $file = null )
    {
        if ( $file == null )
            return false;
        $ext = pathinfo( $file, PATHINFO_EXTENSION );
        switch ( $ext )
        {
            case 'zip':
            case 'rar':
            case '7z':
            case 'tar':
            case 'gz':
                return 'filetype-6.png';

            case 'mp3':
            case 'amr':
                return 'filetype-8.png';

            case 'txt':
            case 'pdf':
            case 'doc':
            case 'rtf':
            case 'djvu':
            case 'xls':
                return 'filetype-4.png';

            case 'jar':
			case 'apk':
            case 'jad':
                return 'filetype-2.png';

            case 'jpg':
            case 'jpeg':
            case 'png':
            case 'gif':
            case 'bmp':
                return 'filetype-5.png';

            case 'sis':
            case 'sisx':
                return 'filetype-3.png';

            case '3gp':
            case 'avi':
            case 'flv':
            case 'mpeg':
            case 'mp4':
                return 'filetype-7.png';

            case 'exe':
            case 'msi':
                return 'filetype-1.png';

            default:
                return 'filetype-9.png';
        }
    }
}
