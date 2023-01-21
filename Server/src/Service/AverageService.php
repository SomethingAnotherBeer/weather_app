<?php
declare(strict_types=1);
namespace App\Service;

class AverageService
{
		
	public function getAverageForSequenceByDate(array $sequence, string $date_format):array
	{
		$current_date = '';

		$current_sum = 0.0;

		$current_start_index = 0;
		$current_end_index = 0;

		$is_processing = true;

		$results = [];

		$current_date = date($date_format, strtotime($sequence[0][0]));

		while ($is_processing)
		{
			while ($current_date === date($date_format, strtotime($sequence[$current_end_index][0])) && $current_end_index < count($sequence) - 1)
			{
				$current_end_index++;
			}

			for ($i = $current_start_index; $i < $current_end_index; $i++)
			{
				$current_sum+= (float)$sequence[$i][1];
			}



			$results[] = [$current_date, $current_sum / ($current_end_index - $current_start_index)];

			$current_date = date($date_format, strtotime($sequence[$current_end_index][0]));
			$current_sum = 0.0;

			$current_start_index = $current_end_index;

			if ($current_start_index === count($sequence) - 1)
			{
				$is_processing = false;
			}

		}

		return $results;

	}


}