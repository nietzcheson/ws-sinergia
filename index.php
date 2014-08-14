<form action="" method="POST">
  <input type="hidden" value="1" name="factura"/>
  <input type="submit"/>
</form>

<?php

require_once "lib/nusoap.php";
require_once "inc/config.inc.php";
require_once "inc/modelo.inc.php";

$datos = datos_principales();
$productos = datos_productos();

if(isset($_POST["factura"])=="1"){

  error_reporting(0);

  $cliente = new nusoap_client(WS,true);
  $error_cliente = $cliente->getError();
  $falta_cliente = $cliente->fault;



  $sutotal = cambio_divisa($datos["subTotal"],$datos["CambioDolares"],$datos["CambioDolares"]);
  $total = cambio_divisa($datos["Total"],$datos["CambioDolares"],$datos["CambioDolares"]);
  $iva = cambio_divisa($datos["TImporte"],$datos["CambioDolares"],$datos["CambioDolares"]);

  $setComprobanteV2012 = array(
  	"usuario" 					=> USER, // Requerido
  	"password" 					=> PASS, // Requerido
  	"Id_SistemaPadre" 			=> "SisFC", // Requerido
  	"EdoComprobante" 			=> "1", // Requerido
  	"Tipo" 						=> "FA", // Requerido
  	"EmRFC" 					=> $datos["EmRFC"], // Requerido CAGE7208162S2
  	"CondicionesPago" 			=> "NA", // No Requerido
  	"FormaPago" 				=> "Pago en parcialidades", // Requerido
  	"Descuento" 				=> "", // No Requerido
  	"motivoDescuento"			=> "", // No Requerido
  	"metodoPago" 				=> "NA", // Requerido
  	"subTotal" 					=> $sutotal, // Requerido
  	"Total"						=> $total, // Requerido
  	"ReID" 						=> "", // No Requerido //Clave cliente
  	"ReNombre" 					=> $datos["ReNombre"], // Requerido
  	"ReRFC" 					=> $datos["ReRFC"], // Requerido
  	"ReCalle" 					=> $datos["ReCalle"], // No Requerido
  	"ReCodigoPostal" 			=> $datos["ReCodigoPostal"], // No Requerido
  	"ReColonia" 				=> $datos["ReColonia"], // No Requerido
  	"ReEstado" 					=> $datos["ReEstado"], // No Requerido
  	"ReLocalidad" 				=> "", // No Requerido
  	"ReMunicipio" 				=> $datos["ReMunicipio"], // No Requerido
  	"ReNoExterior" 				=> $datos["ReNoExterior"], // No Requerido
  	"ReNoInterior"				=> $datos["ReNoInterior"], // No Requerido
  	"ReTel" 					=> "", // No Requerido
  	"RePais" 					=> $datos["RePais"], // Requerido
  	"ReReferencia" 				=> "", // No Requerido
  	"ReCorreo" 					=> "", // No Requerido
  	"TotalImpuestosRetenidos" 	=> "", // Requerido
  	"TotalImpuestoTransladado" 	=> "", // Requerido
  	"RImpuesto" 				=> "IVA", // No Requerido
  	"RImporte" 					=> "", // No Requerido
  	"TImpuesto" 				=> "IVA", // Requerido
  	"TImporte" 					=> $iva, // Requerido
  	"TTasa" 					=> "00", // Requerido
  	"Notas" 					=> $datos["Notas"], // No Requerido
  	"Moneda" 					=> $datos["Moneda"], // No Requerido
  	"TipoCambio" 				=> "", // No Requerido
  	"Vendedor" 					=> "", // No Requerido
  	"OrdCompra" 				=> $datos["OrdCompra"], // No Requerido /*Referencia
  	"Otros" 					=> "", // No Requerido
  	"numCtaPago" 				=> "" // No Requerido
  );

  //Set Comprobante

  $setComprobante = $cliente->call("setComprobanteV2012",$setComprobanteV2012);

  		$setComprobanteDetalle = array(
  			"IdComprobante"				=> $setComprobante["setComprobanteV2012Result"], //Requerido
  			"NoPartida"					=> "", //No Requerido
  			"Cantidad"					=> "10", //Requerido
  			"Descripcion"				=> "Un dos", //Requerido
  			"Importe"					=> "200.00", //Requerido
  			"NoIdentificacion"			=> "Algo", //No Requerido
  			"Unidad"					=> "Pieza", //Requerido
  			"ValorUnitario"				=> "100", //Requerido
  			"PedimentoNo"				=> "", //No Requerido
  			"PedimentoNombre"			=> "", //No Requerido
  			"PedimentoFecha"			=> "", //No Requerido
  			"IVA"						=> "IVa", //Requerido
  			"Notas1"					=> "", //No Requerido
  			"Notas2"					=> ""  //No Requerido
  		);

      $comprobanteDetalle = $cliente->call("setComprobante_Detalle",$setComprobanteDetalle);


//Set Detalle

$sellaComprobante = array(
	"usuario"=>USER,
	"password"=>PASS,
	"IdComprobante"=>$setComprobante["setComprobanteV2012Result"]
);

$sello = $cliente->call("sellaComprobante",$sellaComprobante);

if($sello==""){
  echo "Se ha podido facturar";
}else{
  echo "No se ha podido facturar";
  $_POST["factura"] = 0;
}




}

?>
