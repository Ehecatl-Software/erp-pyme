<?php
class Model_Info{

	public function fecha(){
	
	$dw=date(D);//this day of the week, numeric
		$d=date(j);//this day
		$m=date(m);//this month
		$y=date(o);//this year
		
		switch($dw){
			case 'Mon':
				$dia = 'Lunes';
				break;
			case 'Tue':
				$dia = 'Martes';
				break;
			case 'Wed':
				$dia = 'Miércoles';
				break;
			case 'Thu':
				$dia = 'Jueves';
				break;
			case 'Fri':
				$dia = 'Viernes';
				break;
			case 'Sat':
				$dia = 'Sábado';
				break;
			case 'Sun':
				$dia = 'Domingo';
				break;
			default: break;
		}
		
		switch ($m){
			case 1:
				$mes = 'Enero';
				break;
			case 2:
				$mes = 'Febrero';
				break;
			case 3:
				$mes = 'Marzo';
				break;
			case 4:
				$mes = 'Abril';
				break;
			case 5:
				$mes = 'Mayo';
				break;
			case 6:
				$mes = 'Junio';
				break;
			case 7:
				$mes = 'Julio';
				break;
			case 8:
				$mes = 'Agosto';
				break;
			case 9:
				$mes = 'Septiembre';
				break;
			case 10:
				$mes = 'Octubre';
				break;
			case 11:
				$mes = 'Noviembre';
				break;
			case 12:
				$mes = 'Diciembre';
				break;
			default: break;
		}
	
		$fecha = $dia." ".$d." de ".$mes." de ".$y;
		return $fecha;
	}
}