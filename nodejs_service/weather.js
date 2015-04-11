/**модуль получения погоды из базы**/
var START_YEAR = 1990;
var END_YEAR = 2014;
var DB_DIR = 'app/db/';
var BIG_NUM = 9999999999999999;

var sqlite3 = require('sqlite3');
var RESULT_FAIL = {result:false};
var dbStations = new sqlite3.Database(DB_DIR +'stations.sqlite');

/**
* получение погодных данных в одной точке с 
* заданными координатами на заданную дату
* @param date дата в виде ггггммдд
* @param latitude широта
* @param longitude долгота
* @param callback функция обратного вызова в которую передается результат в виде объекта
**/
function getWeather(date, latitude, longitude, callback){
	var year = date.slice(0, 4);
	if (parseInt(year) < START_YEAR || parseInt(year) > END_YEAR){
		callback(RESULT_FAIL);
		return;
	}
	date = parseInt(date);
	var lat = parseFloat(latitude);
	var lng  = parseFloat(longitude);	
	var stn = null;
	var wban = null;
	var minRast = BIG_NUM;
	var foundLat = null;
	var foundLng = null;
	
	queryStations(function(rows){
		if (rows == null){
			callback(RESULT_FAIL);
			return;
		}
		for ( var i = 0; i < rows.length; i++ ){
			var datebegin = parseInt(rows[i].datebegin);
			var dateend = parseInt(rows[i].dateend);
				
			if ( date < datebegin || date > dateend ) continue;
			var currLat = parseFloat(rows[i].lat);
			var currLng = parseFloat(rows[i].lng);
			var rast = getRast(lat, lng, currLat, currLng);
			
			if ( rast < minRast )
			{
				minRast = rast;
				stn = rows[i].stn;
				wban = rows[i].wban;
				foundLat = currLat;
				foundLng = currLng;
			}
		}
		console.log('Nearest Station: rast='+minRast+' m; stn='+stn+'; wban='+wban+'; lat='+foundLat+'; lng='+foundLng);
		if ( foundLat == null || foundLng == null ){
			callback(RESULT_FAIL);
			return;
		}
		var sql = 'SELECT * FROM meteo WHERE wban='+wban+' AND stn='+stn+' AND thedate='+date;
		queryMeteo(year, sql, function(row){
			if (row == null){
				callback(RESULT_FAIL);
				return;
			}
			var temperature = F2C(row.temperature);
			var pressure = mb2atm(row.pressure);
			var wind = node2ms(row.wind);
			var response = {};
			response.result = true;
			response.temperature = temperature;
			response.pressure = pressure;
			response.wind = wind;
			response.rast = minRast;
			response.stn = stn;
			response.wban = wban;
			response.found_lat = foundLat;
			response.found_lng = foundLng;
			callback(response);
		});		
	});
}


function getWeatherMulti(date, dots, callback){
	
}

/**
* получение данных по станциям из базы
**/
function queryStations(callback){
	if(!dbStations){
		callback(null);
		return;
	}
	var sql = 'SELECT * FROM station';
	dbStations.all(sql, function(err, rows){
		if ( err == null ){
			callback(rows);
		}else{
			callback(null);
		}
	});
}

/**
* получение метеоданных из базы
**/
function queryMeteo(year, sql, callback){
	db = new sqlite3.Database(DB_DIR + 'weather.' + year + '.sqlite');
	db.get(sql, function(err, row){
		if (!err){
			callback(row);
		}else{
			console.log(err);
			callback(null);
		}
	});	
}

/**
* вычисление расстояния между двумя точками на сфере
**/
function getRast(llat1,llng1,llat2,llng2){
	/**pi - число pi, rad - радиус сферы (Земли)**/
	var rad = 6372795;

	/**в радианах**/
	var lat1 = llat1*Math.PI/180;
	var lat2 = llat2*Math.PI/180;
	var long1 = llng1*Math.PI/180;
	var long2 = llng2*Math.PI/180;

	/**косинусы и синусы широт и разницы долгот**/
	var cl1 = Math.cos(lat1)
	var cl2 = Math.cos(lat2)
	var sl1 = Math.sin(lat1)
	var sl2 = Math.sin(lat2)
	var delta = long2 - long1
	var cdelta = Math.cos(delta)
	var sdelta = Math.sin(delta)

	/**вычисления длины большого круга**/
	var y = Math.sqrt(Math.pow(cl2*sdelta,2)+Math.pow(cl1*sl2-sl1*cl2*cdelta,2))
	var x = sl1*sl2+cl1*cl2*cdelta
	var ad = Math.atan2(y,x)
	var dist = ad*rad
	return dist;
}

/**
* перевод из градусов по Фаренгейту в градусы по Цельсию
* @param float t температура в градусах по Фаренгейту
* @return float температура в градусах по Цельсию
**/
function F2C(t)
{
	return 5*(t - 32)/9;
}

/**
* перевод скорости из узлов в м/с
* @param node скорость в узлах
* @return скорость в м/с
**/
function node2ms(node)
{
	return 0.514 * node;
}

/**
* перевод давления из миллибар в атмосферы
* @param mbar давление в миллибарах
* @return давление в атмосферах
**/
function mb2atm(mbar)
{
	return 0.000986923 * mbar;
}

exports.getWeather = getWeather;
exports.getWeatherMulti = getWeatherMulti;