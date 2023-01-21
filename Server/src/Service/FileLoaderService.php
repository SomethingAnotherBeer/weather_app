<?php
namespace App\Service;
use App\Exception\FileException\{FileNotFoundException, CSVFileException};

class FileLoaderService
{

	public function loadCSVFile(string $file_path):array
	{
		$handler = null;
		$data = [];
		$source = [];
		$row = 0;
		$count = 0;

		if (!file_exists($file_path))
		{
			throw new FileNotFoundException("Файл по пути $file_path не найден");
		}

		$handler = fopen($file_path, 'r');

		while(($source = fgetcsv($handler, "1000", ",")) !== false)
		{
			$count = count($source);
			$current_arr = [];

			for ($i = 0; $i < $count; $i++)
			{
				$current_arr[] = $source[$i];
			}

			$data[] = $current_arr;

		}

		if (false === $data)
		{
			throw new CSVFileException("Ошибка чтения файла");
		}

		fclose($handler);

		return $data;

	}


}