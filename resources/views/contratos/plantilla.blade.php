<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contrato de Adhesión</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 98%;
            margin: auto;
            padding: 02px;
        }
        .title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 20px;
        }
        .section {
            margin-bottom: 20px;
        }
        .text-justify {
            text-align: justify;
        }
        .text-left {
            text-align: left;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .signatures {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    .signature {
        text-align: center;
        margin: 0 10px; /* Espaciado opcional entre las firmas */
    }
    </style>
</head>
<body>
    <div class="container">
        <div class="section text-center">
            CONTRATO DE ADHESIÓN POR PRESTACIÓN DE SERVICIOS EDUCATIVOS OXFORD BILINGUAL SCHOOL
        </div>

        <div class="section text-right">
            Correlativo interno Contrato No. {{ $contrato_id }}
            <br>
            Aprobado y registrado según Resolución DIACO: {{ $estudiante->cgshges->gestiones->resolucion_DIACO}}.
        </div>

        <div class="section text-justify">
            En el municipio de {{ $centro['municipio'] }}, del departamento de {{ $centro['departamento'] }}, el día {{ \Carbon\Carbon::now()->locale('es')->isoFormat('D') }} del mes de {{ \Carbon\Carbon::now()->locale('es')->isoFormat('MMMM') }} del año {{ \Carbon\Carbon::now()->format('Y') }}.
        </div>

        <div class="section text-justify">
           <p>Nosotros: Victoria Angelina López de
            Paz, de 51 años de edad, casada, guatemalteca, secretaria y oficinista, de este domicilio, me identifico con Documento Personal de Identificación con Código
            Único de Identificación número dos cinco cinco ocho cinco nueve nueve dos nueve uno seis cero uno (2558599291601) extendido por el Registro
            Nacional de las Personas (RENAP) de la República de Guatemala, actúo en mi calidad de Administrador Único y Representante Legal de CORPORACIÓN
            ACADÉMICA, SOCIEDAD ANÓNIMA lo que acredito con el acta notarial autorizada en la ciudad de Cobán, del departamento de Alta Verapaz, con fecha 3 de junio
            de 2021 por el Notario Carlos Raúl Col Ac, inscrito en el Registro Mercantil bajo el Registro No. 626402, Folio 438, Libro 777 de Auxiliares de Comercio; entidad
            propietaria del centro educativo OXFORD BILINGUAL SCHOOL, ubicado en 2a. calle 16-94 zona 4 de este municipio, lo que acredito con Patente de Comercio de
            Empresa inscrita bajo el número de Registro 692582, Folio 779, Libro 654 de Empresas Mercantiles, emitida por el Registro Mercantil de la República de
            Guatemala.
            </p>

            <p>Y por la otra parte,  {{ $representante->nombres }} {{ $representante->apellidos }}, de {{ \Carbon\Carbon::parse($representante->fecha_nacimiento)->age }}  años de edad, {{ $representante->estado_civil }}, de nacionalidad Guatemalteca, profesión {{ $representante->profesion }}, de este
                domicilio, me identifico con {{ $representante->identificacionDocumento->nombre }} No. {{ $representante->num_documento }} extendido por el Registro Nacional de Personas, de la República de Guatemala , con residencia en {{ $representante->direccion}}, con número de
                teléfono y celular {{ $representante->telefono }}, declarando que la información personal proporcionada, es de carácter confidencial.
                </p>
            <p>Los comparecientes aseguramos ser de los datos de identificación anotados, estar en el libre ejercicio de nuestros derechos civiles y la calidad que se ejercita es
                amplia y suficiente para la celebración del CONTRATO DE ADHESIÓN POR PRESTACIÓN DE SERVICIOS EDUCATIVOS, de conformidad con las siguientes cláusulas:
                </p>
        </div>

    
        

        

   
    
        

        
       
   

        <div class="section text-left">
            <strong> PRIMERA: Información del Educando y Servicio Educativo Contratado. </strong>
      

       <p>  {{ $estudiante->persona->nombres }} {{ $estudiante->persona->apellidos }}, quien cursará el  {{ $estudiante->cgshges->grados->nombre }},  {{ $estudiante->cgshges->cursos->curso }},  {{ $estudiante->cgshges->jornadas->jornada->nombre }}, servicios educativos debidamente autorizados por el
        Ministerio de Educación, de conformidad con las siguientes resoluciones: a) No. 194-2021, de fecha 8 de marzo de 2021; b) DIDEDUC-AC No. 782-2022 de fecha 3
         de junio de 2022, emitidas por la Dirección Departamental de Educación de Alta Verapaz, mismas que se ponen a la vista. </p>
        </div>
       

        <div class="section text-justify">
            <p> <strong> SEGUNDA: Voluntariedad en la  Contratación del Servicio. </strong>
            Manifiesta el Representante del Educando que, conociendo la amplia oferta 
                de instituciones privadas que prestan servicios educativos, de manera voluntaria 
                y espontánea ha elegido al Centro Educativo para la educación académica del 
                educando.</p>
          
       
        </div>

        <div class="section text-justify">
            <p> <strong>TERCERA: Plazo.</strong>
           El servicio educativo convenido en este contrato será válid
                o para el ciclo escolar del año {{ \Carbon\Carbon::now()->format('Y') }}, 
                durante su vigencia no puede ser modificada ninguna de sus cláusulas, las 
                que deberán cumplirse a cabalidad. El Representante del Educando 
                y el Centro Educativo podrán suscribir un nuevo contrato para 
                el ciclo escolar inmediato siguiente, en caso acuerden la 
                continuidad del educando.</p>
        </div>

     

        <div class="section text-justify">
            <p> <strong>CUARTA: Derechos del Educando y su Representante. </strong> 
            El Educando y su Representante como usuarios del servicio educativo contratado, 
                tendrán derecho a:</p>
            <ul>
                <p> <strong>a.   </strong><u>La protección a la vida, salud y seguridad en la adquisición, 
                    consumo y uso de bienes y servicios:</u> Las  instalaciones del Centro 
                    Educativo están dotadas de los servicios básicos, condiciones higiénicas 
                    y adecuadas para que los educandos no corran riesgos que ponga en peligro 
                    su integridad física, siempre y cuando hagan uso correcto de las mismas.
            
                    El Centro Educativo debe promover la formación de hábitos alimenticios 
                    saludables para la nutrición y salud de los educandos. </p>
                    
                    <p><strong>b.    </strong><u>La libertad de Elección y Contratación:</u> El Representante del Educando goza del derecho a elegir 
                    y contratar el Centro Educativo que se adecúe a sus necesidades y capacidades económicas.
                    Los servicios adicionales (bus, venta de útiles y uniformes escolares) pueden ser provistos por el 
                    centro educativo privado o por alguna empresa particular. Estos servicios adicionales no educativos 
                    deberán estar regulados por las entidades gubernamentales encargadas para el efecto. El Centro Educativo 
                    privado no podrá obligar a los Representantes de los Educandos a contratar estos servicios con determinada 
                    empresa, habiendo otras que presten el servicio de igual naturaleza y calidad, a un menor precio; en ese sentido 
                    se debe observar las regulaciones emitidas por el Ministerio de Educación y lo que establece la Ley de Protección 
                    al Consumidor y Usuario.
                    
                    No pueden considerarse servicios adicionales, aquellos materiales e insumos requeridos para el mantenimiento 
                    y funcionamiento del Centro Educativo, así como los utilizados en mejoras escolares. De conformidad con lo que 
                    establece el artículo 6 del Acuerdo Gubernativo No. 36-2015. </p>
                <p><strong>c.    </strong><u>La información veraz, suficiente, clara y oportuna sobre los bienes y servicios:</u> El Centro Educativo privado proporcionará al Representante del 
                    Educando la información completa sobre los bienes y servicios contratados, especialmente horarios de clases, grados y carreras autorizadas por el 
                    Ministerio de Educación, sistemas de evaluación, cursos adicionales, el monto de las cuotas tanto de inscripción como cuota mensual, así como de las 
                    actividades extraescolares de carácter voluntario u optativas que se puedan realizar durante el ciclo escolar respectivo. Las autoridades del Centro 
                    Educativo tienen la obligación de cumplir con las leyes y Acuerdos Ministeriales aplicables a estas actividades.
                </p>
                <p><strong>d.    </strong><u>Utilizar el Libro de Quejas o el medio legalmente autorizado por la Dirección de Atención y Asistencia al 
                    Consumidor para dejar registro de su disconformidad con respecto a un bien adquirido o servicio contratado: </u>
                    El Representante del Educando podrá hacer constar su inconformidad respecto al bien adquirido o el servicio 
                    contratado en el libro de quejas  y  esperar un período de ocho días para que la misma sea resuelta por las 
                    autoridades del Centro Educativo, transcurrido ese tiempo sin que exista una solución satisfactoria podrá 
                    interponer la queja correspondiente ante la Dirección de Atención y Asistencia al Consumidor -DIACO-, quien 
                    procederá según corresponda.</p>
                <p> <strong>e.    </strong><u>Observancia a las leyes y reglamentos en materia educativa.</u> El Centro Educativo deberá velar por el cumplimiento de las normas 
                    aplicables en materia educativa, respetando los valores culturales y derechos inherentes del Educando en su calidad de ser humano, 
                    a su vez proporcionar conocimientos científicos, técnicos y humanísticos a través de una metodología adecuada, así como evaluar con 
                    objetividad y justicia.</p>
            </ul>
        </div>
        
       

        


       


        <div class="section text-justify">
            <strong>Quinta: Obligaciones del Representante del Educando</strong> El Representante del Educando, en armonía con el Artículo 5 de la Ley de Protección al Consumidor y Usuario, tendrá las siguientes obligaciones:
            <ul>
                <p>
                      <strong>a.    </strong>Pagar al Centro Educativo por los servicios proporcionados en el tiempo, modo y condiciones establecidas 
                    mediante el presente contrato.
                    
                </p>
                <p> <strong>b.    </strong>Utilizar los bienes y servicios en observancia a su uso normal, de conformidad con las especificaciones 
                    proporcionadas por el Centro Educativo y cumplir con las condiciones pactadas en el presente contrato, debiendo para tal efecto instruir 
                    al educando sobre el cuidado tanto de las instalaciones, como del mobiliario y equipo del Centro Educativo.En caso de daños y/o perjuicios 
                    ocasionados por el educando, el Representante del Educando será el responsable, siempre y cuando sean debidamente comprobados y atribuidos
                     al mismo.
                </p>
                <p><strong>c.    </strong>Ser orientadores en el proceso educativo de los educandos y velar porque cumplan con las obligaciones establecidas 
                    en las leyes y reglamentos internos del Centro Educativo.
                
                </p>
            </ul>
        </div>

        <div class="section text-justify">
            <p> <strong>Sexta: Cuotas.</strong>
           Como Representante del Educando me comprometo a efectuar únicamente los siguientes pagos, sin necesidad de cobro, ni requerimiento alguno: </p>
        </div>

        <table border="1" style="width: 100%; border-collapse: collapse; text-align: center; font-family: Arial, sans-serif; font-size: 12px;">
            <thead>
                <tr>
                    <th style="padding: 8px; background-color: #f0f0f0;">EN CONCEPTO DE:</th>
                    <th style="padding: 8px; background-color: #f0f0f0;">LA CANTIDAD DE:</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 8px; text-align: left;">a) INSCRIPCIÓN POR EDUCANDO:<br>(UN SÓLO PAGO ANUAL)</td>
                    <td style="padding: 8px;"> Q.{{ $estudiante->cgshges->niveles->costos->first()->Inscripcion ?? 'No disponible' }}</td>
                </tr>
                <tr>
                    <td style="padding: 8px; text-align: left;">b) COLEGIATURA MENSUAL:<br>(10 CUOTAS EN LOS MESES DE ENERO A OCTUBRE)</td>
                    <td style="padding: 8px;"> Q.{{ $estudiante->cgshges->niveles->costos->first()->Mensualidad ?? 'No disponible' }}</td>
                </tr>
            </tbody>
        </table>
      

        <div class="section text-justify">
           <p> Cuotas debidamente autorizadas por el Ministerio de Educación, según resolución a) No.194-2021, de fecha 8 de marzon de 2021; b) DIDEDUC-AC No. 782-2022
            de fecha 3 de junio de 2022, emitidas por la Dirección Departamental de Educación de Alta Verapaz, valores que se informan a continuación: <p>
            
        </div>

        <div class="section text-center">
            <strong>Jornada {{ $estudiante->cgshges->jornadas->jornada->nombre }} </strong>
        </div>

        <table border="1" style="width: 100%; border-collapse: collapse; text-align: center; font-family: Arial, sans-serif; font-size: 12px;">
            <thead>
                <tr>
                    <th style="padding: 8px; background-color: #f0f0f0;">NIVEL DE EDUCACIÓN</th>
                    <th style="padding: 8px; background-color: #f0f0f0;">INSCRIPCIÓN</th>
                    <th style="padding: 8px; background-color: #f0f0f0;">COLEGIATURA MENSUAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td style="padding: 8px;">Pre-primaria</td>
                    <td style="padding: 8px;">Q. 600.00</td> <!-- ⚠️ Datos sin escapar -->
                    <td style="padding: 8px;">Q. 550.00</td> <!-- ⚠️ Datos sin escapar -->
                </tr>
                <tr>
                    <td style="padding: 8px;">Primaria</td>
                    <td style="padding: 8px;">Q. 600.00 <!-- ⚠️ Etiqueta de celda no cerrada -->
                    <td style="padding: 8px;">Q. 600.00</td>
                </tr>
                <tr>
                    <td style="padding: 8px;">Básico</td>
                    <td style="padding: 8px;">Q. 650.00</td>
                    <td style="padding: 8px;">Q. 600.00</td>
                </tr>
                <tr>
                    <td style="padding: 8px;">Bachilerato en Ciencias y Letras</td>
                    <td style="padding: 8px;">Q. 700.00</td>
                    <td style="padding: 8px;">Q. 600.00</td>
                </tr>
            </tbody>
        </table>
        
        <div class="section text-justify">
      

       <p> Para el pago de las cuotas, ambas partes acordamos que sea en forma anticipada, debiendo efectuar el pago durante los primeros diez días hábiles del mes al cual
           corresponde el servicio educativo brindado. </p>

        </div>


        <div class="section text-justify">
            <strong>Séptima: Incumplimiento de Pago</strong>
            <p>En caso que el Representante del Educando se atrase o incumpla con los
                 pagos normados en la cláusula anterior, el Centro Educativo podrá 
                 exigir al Representante del Educando el cumplimiento de las 
                 obligaciones contraídas en el presente contrato.</p>
        </div>


        <div class="section text-justify">
            <strong>Octava: Derechos y Obligaciones de Centro Educativo:</strong> 
            De conformidad con la legislación aplicable y lo establecido en el presente contrato, tendrá los derechos siguientes:
            <ul>
                <p>
                      <strong>a)    </strong>Exigir al Representante del Educando el cumplimiento de los contratos válidamente celebrados. 
                    
                </p>
                <p> <strong>b)    </strong>El libre acceso a los órganos administrativos y judiciales para la solución de conflictos que surjan por la prestación del servicio educativo. 
                </p>
                <p><strong>c)    </strong>Los demás que establecen las leyes del país. 
      
                </p>
            </ul>
            El Centro Educativo deberá cumplir con lo siguiente:
            <ul>
                <p>
                      <strong>a)    </strong>Atender los reclamos formulados por el Representante del Educando.
                    
                </p>
                <p> <strong>b)    </strong>Generar mecanismos para la información continua con el Representante 
                    del Educando, así como crear espacios que promuevan el aprendizaje de los educandos.
                </p>
                <p><strong>c)    </strong>Asegurar un ambiente escolar que favorezca la autoestima, resolución pacíficade 
                    problemas, el reconocimiento de la dignidad humana, el respeto y la valorización de las identidades étnicas 
                    y culturales, la equidad de género, la formación de valores y los derechos humanos. 
                </p>
                <p><strong>d)    </strong>Cumplir con las leyes tributarias del país en lo aplicable.
                </p>
                <p><strong>e)    </strong>Las demás que establecen las leyes del país. 
                </p>
            </ul>
        </div>

        <div class="section text-justify">
            <strong>Novena: Cheques Rechazados</strong>
            <p>Por concepto de cheques rechazados el Centro Educativo podrá 
                cobrar como máximo el valor que por tal motivo debita o cobra 
                el Banco que rechazó el pago del mismo.</p>
        </div>

        <div class="section text-justify">
            <strong>Décima: <u>Traslado o Retiro del Estudiante</u></strong>
            <p> De conformidad con lo establecido por el artículo 38 del Acuerdo 
                Gubernativo número 52-2015 o cualquier otra disposición legal aplicable, 
                el traslado o retiro del educando podrá realizarse en cualquier momento 
                del ciclo escolar a otro Centro Educativo ya sea privado o público, 
                siempre y cuando se cumpla con las regulaciones que para el efecto emita 
                la autoridad competente.</p>
            <p>
                El Representante del Educando debe cancelar la cuota mensual hasta el mes 
                en que efectivamente sea retirado el educando del plantel educativo, sin que 
                esto sea motivo o justificación para retener el expediente.
            </p>
            <p>
                El establecimiento que recibe al estudiante queda obligado a informar sobre 
                el traslado a la Unidad de Planificación de la Dirección Departamental de 
                Educación, manteniendo el mismo código personal del estudiante y dentro de 
                los quince días siguientes de efectuado.
            </p>
        </div>

        <div class="section text-justify">
            <strong>Décima Primera: Copia del Contrato</strong>
            <p>El Centro Educativo deberá entregar una copia del presente 
                contrato, quedando el original en poder de la autoridad del 
                mismo, con el propósito que cada uno de los comparecientes estén 
                enterados de sus derechos y obligaciones para que los ejerciten y 
                cumplan de conformidad con lo establecido. La copia será entregada 
                al Representante  del Educando al momento de firmar el contrato.</p>
                <p>
                    Ambas partes acuerdan que la legalización de las firmas del presente 
                    contrato, correrán por cuenta del Centro Educativo.
                </p>
        </div>


        <div class="section text-justify">
            <strong>Décima Segunda: Derecho de Retracto. </strong>
            <p>El Representante del Educando tendrá derecho a retractarse dentro de un 
                plazo no mayor de cinco días hábiles, contados a partir de la firma del 
                contrato. Si ejercita oportunamente este derecho le serán restituidos 
                en su totalidad los valores pagados, siempre que no hubiere hecho uso 
                del servicio.</p>
        </div>

        <div class="section text-justify">
            <strong>Décima Tercera: Aceptación. </strong>
            <p>Nosotros los comparecientes, damos lectura íntegra al presente 
                contrato, enterados de su contenido, objeto, validez y demás 
                efectos legales, lo ratificamos, aceptamos y firmamos.</p>
        </div>

        <table style="width: 100%; text-align: center;">
            <tr>
                <td>
                    <p>f)__________________________</p>
                 
                    <p>Victoria Angelina López de Paz</p>
                    <p><strong>Administrador Único y Representante Legal
                    </strong></p>
                </td>
                <td>
                    <p>f)__________________________</p>
                  
                    <p>{{ $representante->nombres }} {{ $representante->apellidos }}</p>
                    <p><strong>Representante del Educando</strong></p>
                </td>
            </tr>
        </table>
        

      

    </div>
</body>



</html>
