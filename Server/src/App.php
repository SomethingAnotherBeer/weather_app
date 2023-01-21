<?php
namespace App;
use App\Service\{FileLoaderService, DataExtracterService, AverageService};
use App\Exception\FileException\FileException;

class App
{
	private array $dependencies;

	public function __construct(array $dependencies)
	{
		$this->dependencies = $dependencies;
	}


	public function run():void
	{
		try
		{
			$fileLoader = new FileLoaderService();
			$dataExtracter = new DataExtracterService();
			$averageService = new AverageService();

			$csv_arr = $fileLoader->loadCSVFile($this->dependencies['files']['weather']);

			$extracting_options = [
				'available_indexes' => [0,1],
				'checkers' => [
					'0' => ['date'],
					'1' => ['number']
				]

			];

			$extracted_data = $dataExtracter->extractData($csv_arr, $extracting_options);

			$extracted_prepared_data = $extracted_data['prepared_data'];
			$extracted_unexpected_data = $extracted_data['unexpected_data'];


			$average_days = $averageService->getAverageForSequenceByDate($extracted_prepared_data, "Y-m-d");
			$average_weeks = $averageService->getAverageForSequenceByDate($extracted_prepared_data, "W");
			$average_months = $averageService->getAverageForSequenceByDate($extracted_prepared_data, "m");

			$averages = [$average_days, $average_weeks, $average_months];


			usort($averages[0], fn ($a, $b) => strtotime($a[0]) - strtotime($b[0]));

			for($i = 1; $i < count($averages); $i++)
			{
				usort($averages[$i], fn ($a, $b) => (int)$a[0] - (int)$b[0]);
			}


			$average = [
				'days' => $averages[0],
				'weeks' => $averages[1],
				'months' => $averages[2],

			];


			$average['weeks'] = $this->getPreparedWeeks($average['weeks']);
			
			echo json_encode($average, JSON_UNESCAPED_UNICODE);



		}


		catch(FileException $e)
		{
			die($e->getMessage());
		}


		catch(\Exception $e)
		{
			die ($e->getMessage());
		}

	}


	private function getPreparedWeeks(array $weeks):array
	{
		$previous_year_week_value = 0.0;
		$prepared_weeks = [];

		$previous_year_week_number = $weeks[count($weeks) - 1][0];


		if (53 === (int)$previous_year_week_number)
		{
			$previous_year_week_value = $weeks[count($weeks) - 1][1];
			unset($weeks[count($weeks) - 1]);

			return [
				'current_year_weeks' => $weeks,
				'previous_year_week' => $previous_year_week_value,
			];

		}

		else
		{
			return $weeks;
		}


	}


}