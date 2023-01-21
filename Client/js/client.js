
const server_name = 'http://weatherapp111/';

const menu_descriptions = [
	
	{
		'buttonName':'дни',
		'buttonId':'days',
	},

	{	
		'buttonName': 'недели',
		'buttonId': 'weeks',
	},

	{
		'buttonName': 'месяцы',
		'buttonId': 'months',
	},

];



function getDataSet(data)
{
	let keys = data.map((item) => item[0]);
	let values = data.map((item) => item[1]);

	return {'keys':keys, 'values': values}



}


function getWeeks(weeks_obj)
{

	const current_year_weeks = weeks_obj['current_year_weeks'];
	const previous_year_week = ["-1", weeks_obj['previous_year_week']];

	current_year_weeks.unshift(previous_year_week);

	return current_year_weeks;

}


function getMenuItem(menu_item_description)
{
	const menu_item = document.createElement('li');
	const menu_item_button = document.createElement('button');

	menu_item_button.id = menu_item_description['buttonId'];
	menu_item_button.innerText = menu_item_description['buttonName'];

	menu_item.append(menu_item_button);

	return menu_item;
}




function createChart()
{
	let previous_chart = null;

	return function(chart_node, data)
	{
		if (previous_chart)
		{
			previous_chart.destroy();
		}


		chart = new Chart(chart_node, {
			type: 'line',

			data: {
				labels: data['keys'],
				datasets: [{
					label: 'График',
					backgroundColor: 'rgb(43, 100, 130)',
					borderColor: 'rgb(43, 100, 130)',
					data: data['values']

				}],
			},
		});

		previous_chart = chart;

	}

}


window.onload = function(){

	const request = new XMLHttpRequest();

	let days = [];
	let weeks = [];
	let months = [];

	let canvas = null;

	let menu = null;
	let node_area = null;

	const datasets = new Map();
	const menu_items = [];

	let chart_create_func = null;

	request.onreadystatechange = function()
	{
		if (request.readyState === 4 && request.status === 200)
		{
			response = JSON.parse(request.responseText);

			days = response['days'];
			months = response['months'];
			weeks = (Array.isArray(response['weeks'])) ? response['weeks'] : getWeeks(response['weeks']);

			
			datasets.set('days', getDataSet(days));
			datasets.set('months', getDataSet(months));
			datasets.set('weeks', getDataSet(weeks));


			node_area = document.querySelector('body .container');
			menu = document.createElement('ul');
			menu.className = 'base-menu';

			for (let menu_description of menu_descriptions)
			{
				menu.append(getMenuItem(menu_description));
			}

			node_area.prepend(menu);

			for(let menu_description of menu_descriptions)
			{
				menu_items.push(document.getElementById(menu_description['buttonId']));
			}

			canvas = document.getElementById('myChart').getContext('2d');
			chart_create_func = createChart();

			for(let menu_item of menu_items)
			{
				menu_item.addEventListener('click', () => chart_create_func(canvas, datasets.get(menu_item.id)));

			}

		}
	}


	request.open('GET', server_name);
	request.send();


}