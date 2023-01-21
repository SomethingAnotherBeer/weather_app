<?php
declare(strict_types=1);
namespace App\Service;
use App\Exception\ValidatingException;

class DataExtracterService
{

	private array $checkers;
	private array $checkers_messages;


	public function __construct()
	{
		$this->checkers = [
			'date' => fn(string $data_item):bool => ($data_item && strtotime($data_item)) ? true : false,
			'number' => fn (string $data_item):bool => ($data_item && (is_float((float)$data_item)) || is_int((int)$data_item)) ? true : false,   

		];


		$this->checkers_messages = [
			'date' => fn(string $item) => "Недопустимый формат даты: $item",
			'number' => fn(string $item) => "Объект $item не является числом", 

		];

	}


	public function extractData(array $data, array $options):array
	{
		$available_indexes = (array_key_exists('available_indexes', $options)) ? $options['available_indexes'] : array_keys($options);

		$result = (array_key_exists('checkers', $options)) ? $this->extractWithCheck($data, $available_indexes, $options['checkers']) : $this->extract($data, $available_indexes);

		return $result;

	}


	private function extract(array $data, array $available_indexes):array
	{
		$prepared_data = [];
		$prepared_data_item = [];
		sort($available_indexes);


		for ($i = 0; $i < count($data); $i++)
		{
			for ($j = $available_indexes[0]; $j < count($available_indexes); $j++)
			{

				$prepared_data_item[] = $data[$i][$j];
 			}

 			$prepared_data[] = $prepared_data_item;
 			$prepared_data_item = [];

		}

		return $prepared_data;

	}


	private function extractWithCheck(array $data, array $available_indexes, array $checkers):array
	{
		$prepared_data = [];
		$prepared_data_item = [];
		$unexpected_data = [];
		$current_check = true;

		sort($available_indexes);

		for ($i = 0; $i < count($data); $i++)
		{
			$current_check = true;

			for ($j = $available_indexes[0]; $j < count($available_indexes); $j++)
			{
				if (!$current_check)
				{
					continue;
				}

				if (array_key_exists((string)$j, $checkers))
				{
					foreach ($checkers[(string)$j] as $checker)
					{
						$current_check = (array_key_exists($checker, $this->checkers)) ? $this->checkers[$checker]($data[$i][$j]) : true;

					}
				}

				if ($current_check)
				{
					$prepared_data_item[] = $data[$i][$j]; 
				}
				else
				{
					$unexpected_data[] = ["row: $i, column: $j", $data[$i][$j], $this->checkers_messages[$checker]($data[$i][$j])];
				}


			}

			if ($prepared_data_item) $prepared_data[] = $prepared_data_item;
			$prepared_data_item = [];

		}

		return ['prepared_data' => $prepared_data, 'unexpected_data' => $unexpected_data];

	}





}