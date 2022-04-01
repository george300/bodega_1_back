<?php


use App\Http\Controllers\SimuladorController;
use Illuminate\Support\Facades\Route;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
   //para la auditoria
Route::resource('audits','AuditController');
Route::resource('audits','AuditController');
Route::resource('document','DocumentosController');
Route::post('archivoDelete','DocumentosController@destroy');
Route::post('documentDelete','DocumentosController@documentDelete');
Route::get('getDocumentos','DocumentosController@getDocumentos');
Route::get('getDocumentosDocente','DocumentosController@getDocumentosDocente');
Route::post('audits/eliminar','AuditController@eliminarAudit');

Route::get('userInformacion','UsuarioController@userInformacion');

Route::post('codigos/importar','CodigoLibrosController@importar');
Route::post('codigos/bloquear','CodigoLibrosController@bloquearCodigos');
Route::post('codigos/revision','CodigoLibrosController@revision');

//==============API CAPACITACIONES=============================
Route::resource('capacitacion','CapacitacionController');
Route::get('delete_agenda_asesor/{id}','CapacitacionController@delete_agenda_asesor');
Route::get('temasCapacitacion','CapacitacionController@temasCapacitacion');
//capacitacion temas
Route::resource('capacitacionTema','CapacitacionTemaController');


//==============FIN APIS CAPACITACIONES=============================
//==============API BARCODE LIQUIDACION=============================

Route::get('/bliquidacion/{contrato}','TemporadaController@bliquidacion_milton');
Route::post('bliquidacionSistema','TemporadaController@bliquidacionSistema');
//==============FIN DE APIS BARCODE LIQUIDACION=============================


//==============RUTAS PARA COLEGIO =============================
Route::resource('colegio-docente','ColegiosController');
Route::get('colegio-ingreso','ColegiosController@ingreso');
Route::get('colegio-libros','ColegiosController@listadoLibros');
Route::get('colegio-desglose','ColegiosController@Listadodesglose');
Route::get('colegio-tipoJuegos','ColegiosController@tipoJuegos');
Route::get('colegio-accesoLibros','ColegiosController@accesoLibros');
Route::get('institucionesColegios','ColegiosController@institucionesColegios');

Route::get('periodoInstitucion', 'PeriodoController@institucion');
Route::apiResource('menu','MenuController');
Route::get('menu_unidades_libros/{id}','LibroController@menu_unidades_libros');
Route::post('cuadernos_usuario_libro', 'CuadernoController@cuadernos_usuario_libro');
Route::get('colegio-planificacion_asignatura','ColegiosController@ColegioPlanificacionAsignatura');
Route::get('desgloselibrousuario/{id}','LibroController@desgloselibrousuario');

Route::get('get_links_libro/{id}','LibroController@get_links_libro');
Route::post('guardar_link_libro','LibroController@guardar_link_libro');

Route::get('institucionesResportes','CodigosLibrosGenerarController@institucionesResportes');

Route::get('asignaturasDoc/{id}','AsignaturaController@asignaturasDoc');
Route::apiResource('asignatura','AsignaturaController');
Route::post('guardar_asignatura_usuario', 'AsignaturaDocenteController@guardar_asignatura_usuario');
Route::apiResource('curso','CursoController');
Route::post('curso_libro_docente', 'CursoController@curso_libro_docente');
Route::get('verif_asignatura_por_curso/{id}', 'CursoController@verif_asignatura_por_curso');

Route::post('/register', 'AuthController@register');
//api para traer los paralelos y el grado
Route::get('infoRegistro','UsuarioController@infoRegistro');
Route::get('selectInstitucion','InstitucionController@selectInstitucion');
Route::get('selectArea','ColegiosController@selectArea');
Route::post('asignar_asignatura_colegio','ColegiosController@asignar_asignatura_colegio');
Route::get('asignaturas_x_colegio','ColegiosController@asignaturas_x_colegio');
Route::get('colegios/permisos','ColegiosController@permisos');
Route::get('eliminaAsignacionColegio/{id}','ColegiosController@eliminaAsignacionColegio');

//==============API PARA PERMISOS ROOT=============================
Route::resource('permisos','ConfiguracionController');
//==============FIN DE APIS PERMISOS ROOT=============================

////==============FIN RUTAS PARA COLEGIO =============================

Route::get('estudianteCurso', 'EstudianteController@estudianteCurso');
Route::apiResource('ciudad','CiudadController');
Auth::routes(['register' => false]);
//ruta para restaurar el password desde un usuario
Route::post('restaurarDatos', 'UsuarioController@restaurarDatos');
// cargar periodo a codigo curso
Route::get('agregarPeriodoCurso','CodigosLibrosGenerarController@agregarPeriodoCurso');
// cargar periodo a los utlimos cursos
Route::get('agregarPeriodoCursoUltimo','CodigosLibrosGenerarController@agregarPeriodoCursoUltimo');
//api para email



//============APIS TEMPORADAS===========================
Route::post('temporadasapi','TemporadaController@generarApiTemporada');
//api para listado de instituciones para milton
Route::get('instituciones_facturacion','TemporadaController@instituciones_facturacion');

//apis  para la tabla  temporadas
Route::resource('temporadas','TemporadaController')->except(['edit','create']);

Route::get('/liquidacion/{contrato}','TemporadaController@liquidacionMilton');
Route::get('temporadas/liquidacion/{contrato}','TemporadaController@liquidacion');
//api para traer los contratos para que los asesores puedan visualizar
Route::post('temporadas/asesor/contratos','TemporadaController@asesorcontratos');
//api para eliminar como prueba
Route::post('temporadas/eliminar','TemporadaController@eliminarTemporada');
//activar o desativar la data de la tabla temporada
Route::post('temporadas/desactivar','TemporadaController@desactivar');
Route::post('temporadas/activar','TemporadaController@activar');
Route::post('temporadas/docente','TemporadaController@agregardocente');
//api para traer las instituciones por ciudad
Route::post('traerinstituciones','TemporadaController@traerInstitucion');
Route::get('traerInstitucion','InstitucionController@traerInstitucion');
//api para traer los periodos
Route::post('traerperiodos','TemporadaController@traerperiodos');

//api para traer los usuarios por periodo
Route::post('usuariosXperiodoSierra','PeriodoController@usuariosXperiodoSierra');
Route::post('usuariosXperiodoCosta','PeriodoController@usuariosXperiodoCosta');

//api para traer las instituciones por ciudad
Route::post('traerprofesor','TemporadaController@traerprofesores');


//Api para milton gel
  Route::get('temporada/datos','TemporadaController@temporadaDatos');
//=========================FIN API TEMPORADAS===========================

//=========================API PARA LIQUIDACIONES==========================
Route::resource('verificacion','VerificacionController');
Route::get('liquidacion/verificacion/{contrato}','VerificacionController@liquidacionVerificacion');
Route::get('liquidacion/verificacion/{contrato}/{numero}','VerificacionController@liquidacionVerificacionNumero');
Route::get('liquidacion/codigosperdidos/{contrato}','VerificacionController@codigosperdidos');
Route::get('liquidacion/codigosmovidos/{contrato}','VerificacionController@codigosmovidos');


//=========================FIN DE API DE LIQUIDACIONES=======================

//=========================API PARA LIQUIDACIONES CON CODIGOS DE BARRAS==========================
Route::resource('bc_verificacion','VerificacionBarrasController');
Route::get('bc_liquidacion/verificacion/{contrato}','VerificacionBarrasController@liquidacionVerificacion');
Route::get('bc_liquidacion/verificacion/{contrato}/{numero}','VerificacionBarrasController@liquidacionVerificacionNumero');

//=========================FIN DE API DE LIQUIDACIONES con codigos de barras=======================

//=========================API PARA PREGUNTAS FRECUENTES====================
Route::resource('preguntasfaq', 'PreguntasfaqController');
Route::post('cambioEstadoPregunta','PreguntasfaqController@cambioEstadoPregunta');


//========================FIN DE APIS PARA PREGUNTAS FRECUENTES=============

// Route::get('datoEscuela','AdminController@datoEscuela');
//======================APIS PARA WEBINAR=======================
Route::get('verificarCedula','AuthController@verificarCedula');
Route::get('webinarAsistencia','SeminarioController@webinarAsistencia');
Route::get('obtenerWebinars','SeminarioController@obtenerWebinars');
Route::get('sumarEncuestasDescargadas','SeminarioController@sumarEncuestasDescargadas');
//=======================FIN APIS PARA WEBINAR================

//========================APIS PARA MATRICULAS=================================
Route::get('cursosInstitucion', 'CursoController@cursosInstitucion');
Route::get('estudiante/matricula', 'CursoController@estudianteMatricula');
Route::post('updateEstudiante','CursoController@updateEstudiante');
Route::post('updateEstudianteAdministrador','CursoController@updateEstudianteAdministrador');
Route::post('guardarInformacionNiveles','CursoController@guardarInformacionNiveles');
Route::get('institucionTraerPeriodo','CursoController@institucionTraerPeriodo');
Route::post('guardarFotoMatricula','CursoController@guardarFotoMatricula');
Route::get('valores/pensiones','CursoController@valoresPensiones');
//api para traer los paralelos y estudiantes
Route::get('estudianteParalelo','CursoController@estudianteParalelo');
//api para traer los periodos niveles
Route::get('nivelPeriodoInstitucion','CursoController@nivelPeriodoInstitucion');
//para guardar los paralelos
Route::post('guardarParalelos','CursoController@guardarParalelos');
//para eliminar los paralelos
Route::get('eliminarParalelo/{id}','CursoController@eliminarParalelo');
//api para guardar las pensiones
Route::post('guardarComprobantepension','CursoController@guardarComprobantepension');
//para traer los niveles de una institucion por periodo
Route::get('nivelesInstitucion','CursoController@nivelesInstitucion');
//PARA EDITAR los valrores por cada nivel
Route::post('editarNiveles','CursoController@editarNiveles');
//para cambiar el estado del estudiante
Route::get('cambiarEstadoMatricula','CursoController@cambiarEstadoMatricula');
Route::get('LegalizarMatricula','CursoController@LegalizarMatricula');
Route::get('validarPagos','CursoController@validarPagos');

// APIS ADMINSTRADOR MATRICULAS
Route::apiResource('matriculas_admin','MatriculaController');
Route::get('listado_matriculas/{instituicion}/{periodo}/{filtro}/{export_excel}','MatriculaController@listado_matriculas');
Route::get('busqueda_estudiante_mat/{periodo}/{institucion}/{tipo}/{filtro}','MatriculaController@busqueda_estudiante_mat');
Route::get('get_cuotas/{id_matricula}','MatriculaController@get_cuotas');
Route::post('guardar_pago_matricula','MatriculaController@guardar_pago_matricula');
Route::get('combos_matricula/{instituicion}','MatriculaController@combos_matricula');
Route::post('aplicar_becas','MatriculaController@aplicar_becas');
Route::post('procesar_pagos','MatriculaController@procesar_pagos');
Route::post('procesar_becas','MatriculaController@procesar_becas');
Route::post('procesar_matriculas','MatriculaController@procesar_matriculas');
Route::post('editar_cuotas','MatriculaController@editar_cuotas');
Route::get('enviar_recordatorio','MatriculaController@enviar_recordatorio');
Route::get('editar_codigos_masivos','SeminarioController@editar_codigos_masivos');

Route::get('guardarData','AdminController@guardarData');
Route::get('pruebaData','AdminController@pruebaData');

//========================FIN APIS PARA MATRICULAS=============================
//========================APIS PARA SEGUIMIENTO=================================
Route::get('asesor/seguimiento','SeguimientoInstitucionController@visitas');
Route::post('guardarSeguimiento','SeguimientoInstitucionController@guardarSeguimiento');
Route::post('muestra','SeguimientoInstitucionController@muestra');
Route::post('GuardarInstitucionTemporal','SeguimientoInstitucionController@GuardarInstitucionTemporal');
Route::post('seguimiento-eliminar','SeguimientoInstitucionController@eliminar');
Route::post('seguimiento-registrar','SeguimientoInstitucionController@registrar');
Route::resource('seguimiento','SeguimientoInstitucionController');
//========================FIN APIS PARA SEGUIMIENTO=================================
//========================APIS PARA REPORTERIA=================================
Route::get('reporteria','UsuarioController@reporteria');

//========================FIN APIS PARA REPORTERIA=============================
//========================APIS PARA TICKETS=================================
Route::resource('ticket', 'TicketController');
//========================FIN APIS PARA TICKETS=============================

//========================APIS PARA SIMULADOR=============
Route::resource('simulador','SimuladorController');
Route::post('asignarSimulador','SimuladorController@asignarSimulador');
Route::post('quitarSimulador','SimuladorController@quitarSimulador');
Route::post('cursosLibrosSimulador','SimuladorController@cursosLibrosSimulador');

//========================FIN DE APIS PARA SIMULADOR=============

//=========================APIS PARA HOME========================
Route::get('escuelasAsesor','UsuarioController@escuelasAsesor');
Route::get('contratosAsesor','UsuarioController@contratosAsesor');

//=========================FIN DE APIS PARA HOME=================


Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('user', 'HomeController@index');
Route::post('addContenidopost', 'CursoController@addContenidoD');
// Route::post('/login', 'AuthController@login');


Route::apiResource('usr','UsuarioController');
Route::post('eliminarUsuario','UsuarioController@eliminarUsuario');
Route::apiResource('institucion','InstitucionController');
Route::apiResource('admin','AdminController');
Route::apiResource('vendedor','VendedorController');

Route::apiResource('docente','DocenteController');
//para traer la cantidad de evaluaciones del docente
Route::get('cantEvaluacionesDocente','DocenteController@cantEvaluacionesDocente');
Route::get('getUserAdmin','DocenteController@getUserAdmin');
Route::apiResource('estudiante','EstudianteController');
Route::apiResource('contenido','ContenidoController');
Route::get('eliminarContenido','ContenidoController@eliminarContenido');
Route::get('teletareasunidades/{id}','ContenidoController@teletareasunidades');

Route::get('teletareasunidades_tema/{id}','ContenidoController@teletareasunidades_tema');
Route::get('teletarea_asignatura/{id}','ContenidoController@teletarea_asignatura');
// Route::apiResource('asignatura','AsignaturaController');
// Route::apiResource('curso','CursoController');
Route::get('cursos_evaluaciones/{id}/{institucion}','CursoController@cursos_evaluaciones');
Route::get('curso_asig_docente/{id}','CursoController@curso_asig_docente');
// Route::get('verif_asignatura_por_curso/{id}', 'CursoController@verif_asignatura_por_curso');
Route::post('cargar_asignatura_curso', 'CursoController@cargar_asignatura_curso');
// Route::post('guardar_asignatura_usuario', 'AsignaturaDocenteController@guardar_asignatura_usuario');
Route::get('asignaturas_crea_docente/{id}','AsignaturaDocenteController@asignaturas_crea_docente');
Route::get('deshabilitarasignatura/{id}','AsignaturaDocenteController@deshabilitarasignatura');
// Route::apiResource('ciudad','CiudadController');
Route::apiResource('region','RegionController');
Route::apiResource('rol','RolController');
Route::apiResource('periodo','PeriodoController');
Route::get('periodoRegion','PeriodoController@periodoRegion');
Route::post('periodo/activar','PeriodoController@activar');
Route::post('periodo/desactivar','PeriodoController@desactivar');
Route::apiResource('juegos','JuegosController');
Route::get('juegos_tema/{id}','JuegosController@juegos_tema');
Route::get('juegos_unidad/{id}','JuegosController@juegos_unidad');
Route::get('juegos_asignatura/{id}', 'JuegosController@juegos_asignatura');
Route::apiResource('libros','LibroController');
//ruta para el crud de libros
Route::get('listaLibro','LibroController@listaLibro');
Route::post('guardarLibro','LibroController@guardarLibro');
Route::post('eliminarLibro','LibroController@eliminarLibro');
Route::post('activarLibro','LibroController@activarLibro');

//fin de ruta para el crud de libros
// Route::get('menu_unidades_libros/{id}','LibroController@menu_unidades_libros');
Route::get('unidades_asignatura/{id}','LibroController@unidades_asignatura');
// Route::get('desgloselibrousuario/{id}','LibroController@desgloselibrousuario');




Route::apiResource('codigoslibros','CodigosLibrosController');
Route::get('codigoslibrosEstudiante','CodigosLibrosController@codigoslibrosEstudiante');
Route::get('codigos_libros_estudiante/{id}','CodigosLibrosController@codigos_libros_estudiante');
Route::post('addContenido', 'CursoController@addContenido');
Route::get('getContenido','CursoController@getContenido');
Route::get('getContenidoTodo','CursoController@getContenidoTodo');
Route::get('eliminarContenido','CursoController@eliminarContenido');
Route::get('librosEstudiante','LibroController@librosEstudiante');
Route::post('postLibroCurso','CursoController@postLibroCurso');
Route::get('librosCurso','CursoController@librosCurso');
Route::get('librosCursoEliminar','CursoController@librosCursoEliminar');
Route::get('getTareasDocentes','CursoController@getTareasDocentes');
Route::post('postCalificacion', 'CursoController@postCalificacion');
Route::post('quitarTareaEntregada', 'CursoController@quitarTareaEntregada');
//ruta para filtrar los estudiantes por cedula
Route::get('busquedaFiltroEstudiante','EstudianteController@busquedaFiltroEstudiante');
Route::get('getEstudiantes','CursoController@getEstudiantes');
// Route::get('estudianteCurso', 'EstudianteController@estudianteCurso');
Route::post('estudiantesEvalCurso', 'EstudianteController@estudiantesEvalCurso');
Route::get('tareaEstudiantePendiente','EstudianteController@tareaEstudiantePendiente');
Route::get('tareaEstudianteRealizada','EstudianteController@tareaEstudianteRealizada');
Route::post('addTareaContenido', 'CursoController@addTareaContenido');
Route::get('tareas', 'DocenteController@tareas');
Route::get('contenidos', 'DocenteController@contenidos');
Route::get('calificacion', 'CursoController@Calificacion');
Route::post('addClase', 'EstudianteController@addClase');
Route::post('verificarCursoEstudiante', 'EstudianteController@verificarCursoEstudiante');
// Route::get('selectInstitucion','InstitucionController@selectInstitucion');
Route::get('estudiantejuegos','JuegosController@juegosEstudainte');
Route::post('guardarTarea','CursoController@guardarTarea');
// Route::post('restaurar', 'UsuarioController@restaurar');
//ruta para restaurar
Route::post('restaurarPassword', 'UsuarioController@restaurarPassword');
Route::post('cambio_password', 'UsuarioController@passwordC');
Route::post('perfil', 'UsuarioController@perfil');
Route::get('obtenerPerfiles', 'UsuarioController@obtenerPerfiles');
Route::post('guardarPerfil','UsuarioController@guardarPerfil');
Route::post('eliminarPerfil','UsuarioController@eliminarPerfil');
Route::post('quitarTareaEntregada', 'CursoController@quitarTareaEntregada');
// Route::post('curso_libro_docente', 'CursoController@curso_libro_docente');
Route::get('areaSelect', 'AreaController@select');
// ===================== API ==========================
Route::apiResource('cursolibro','CursoLibroController');
Route::get('libro','LibroController@aplicativo');
Route::get('selectlibro','LibroController@libro');
Route::get('selectplanlector','LibroController@planlector');
Route::post('libroFree','LibroController@libroFree');
Route::post('planlectorFree','LibroController@planlectorFree');
Route::get('listaFree','LibroController@listaFree');
Route::get('listaFreePlanlector','LibroController@listaFreePlanlector');
Route::post('setNivelFree','LibroController@setNivelFree');
Route::get('eliminarLibroFree','LibroController@eliminarLibroFree');
Route::get('eliminarPlanlectorFree','LibroController@eliminarPlanlectorFree');
Route::get('libroEstudiante','LibroController@aplicativoEstudiante');
Route::post('quitarlibroestudiante','LibroController@quitarlibroestudiante');
// ===================== RUTAS CUADERNO ==========================
Route::get('codigosCuaderno','CodigosLibrosController@codigosCuaderno');
Route::apiResource('cuadernos','CuadernoController');
Route::get('cuaderno','CuadernoController@aplicativo');
Route::get('getCuadernos','CuadernoController@getCuadernos');
Route::post('cuadernoEliminar','CuadernoController@cuadernoEliminar');

// ===================== FIN RUTA CUADERNO ==========================
Route::get('guia','GuiaController@aplicativo');
Route::get('planlector','PlanLectorController@aplicativo');
Route::get('material','MaterialApoyoController@aplicativo');
Route::get('materialapoyo_unidad/{id}','MaterialApoyoController@materialapoyo_unidad');
Route::get('materialapoyolibro_tema/{id}','MaterialApoyoController@materialapoyolibro_tema');
Route::get('planificacion','PlanificacionController@aplicativo');
Route::get('planificacion_asignatura/{id}','PlanificacionController@planificacion_asignatura');
Route::get('video','VideoController@aplicativo');
Route::get('videos_libro_unidad/{id}','VideoController@videos_libro_unidad');
Route::get('videos_libro_tema/{id}','VideoController@videos_libro_tema');
Route::get('usuario','UsuarioController@aplicativo');
Route::get('aplicativobase','UsuarioController@aplicativobase');
Route::get('usuarios','UsuarioController@index');
Route::apiResource('notaEstudiante','NotaEstudianteController');
Route::apiResource('planificaciones','PlanificacionesController');
Route::get('buscaUsuario','UsuarioController@buscaUsuario');
Route::get('ciudades','CiudadController@ciudades');
Route::get('verInstitucionCiudad/{id}','InstitucionController@verInstitucionCiudad');
Route::get('verificarInstitucion/{id}','InstitucionController@verificarInstitucion');
Route::post('asignarInstitucion','InstitucionController@asignarInstitucion');
Route::apiResource('seminario', 'SeminarioController');
Route::get('get_seminarios/{id_periodo}', 'SeminarioController@get_seminarios');
Route::get('actualiza_periodo_seminario', 'SeminarioController@actualiza_periodo_seminario');
Route::get('obtener_seminarios_docente', 'SeminarioController@obtener_seminarios_docente');
Route::get('obtener_webinars_docente', 'SeminarioController@obtener_webinars_docente');


Route::get('get_seminarios_docente/{id}', 'SeminarioController@get_seminarios_docente');
Route::get('get_seminarios_webinar/{id}', 'SeminarioController@get_seminarios_webinar');
//para registra  la asistencia en un seminario
Route::get('SeminarioAsistencia','SeminarioController@SeminarioAsistencia');
Route::get('resumenWebinar/{periodo}','SeminarioController@resumenWebinar');
Route::get('get_webinars','SeminarioController@get_webinars');
Route::get('get_preguntas_seminario', 'SeminarioController@get_preguntas_seminario');
Route::post('save_encuesta', 'SeminarioController@save_encuesta');
Route::get('reporte_seminario/{id}', 'SeminarioController@reporte_seminario');
Route::get('get_instituciones', 'SeminarioController@get_instituciones');
Route::post('guardar_seminario', 'SeminarioController@guardar_seminario');
Route::get('eliminar_seminario/{id}', 'SeminarioController@eliminar_seminario');
Route::get('get_periodos_seminarios', 'SeminarioController@get_periodos_seminarios');
Route::apiResource('inscripcion', 'InscripcionController');
Route::apiResource('nivel', 'NivelController');
//api para eliminar el nivel
Route::post('niveleliminar','NivelController@niveleliminar');
Route::get('buscarSeminario', 'SeminarioController@buscarSeminario');
Route::get('eliminarSeminario', 'SeminarioController@eliminarSeminario');
Route::get('asignaturas','AsignaturaController@asignatura');
Route::get('eliminarTarea','CursoController@eliminarTarea');
Route::get('eliminarCurso','CursoController@eliminarCurso');
Route::post('eliminarAlumno','CursoController@eliminarAlumno');
Route::post('setContenido','ContenidoController@setContenido');
Route::post('setPlanificacion','PlanificacionesController@setPlanificacion');

//apis evaluaciones
Route::apiResource('evaluacion', 'EvaluacionController');
Route::apiResource('pregunta', 'PreguntaController');
Route::get('preguntasDocente/{id}', 'PreguntaController@preguntasDocente');
Route::apiResource('tema', 'TemaController');
Route::post('temasignunidad','TemaController@temasignunidad');
Route::get('temAsignaruta/{id}','TemaController@temAsignaruta');
Route::post('eliminar_tema','TemaController@eliminar_tema');
Route::apiResource('pregEvaluacion', 'PregEvaluacionController');
Route::post('pregEvaluacionGrupo', 'PregEvaluacionController@pregEvaluacionGrupo');
Route::post('preguntasxbanco', 'PregEvaluacionController@preguntasxbanco');
Route::post('preguntasxbancoDocente', 'PregEvaluacionController@preguntasxbancoDocente');
Route::post('preguntasxbancoProlipa', 'PregEvaluacionController@preguntasxbancoProlipa');
Route::post('pregEvaluacionEstudiante', 'PregEvaluacionController@pregEvaluacionEstudiante');
Route::apiResource('respEvaluacion', 'CalificacionEvalController');
Route::post('verifRespEvaluacion', 'CalificacionEvalController@verifRespEvaluacion');
Route::apiResource('evaluacionResponder', 'EvaluacionController');
Route::post('cargarOpcion', 'PreguntaController@cargarOpcion');
Route::get('quitarOpcion/{id}','PreguntaController@quitarOpcion');
Route::post('editarOpcion','PreguntaController@editarOpcion');
Route::get('verOpciones/{id}','PreguntaController@verOpciones');
Route::get('evaluacionEstudiante/{id}', 'CalificacionEvalController@evaluacionEstudiante');
Route::get('quitarPregEvaluacion/{id}','PregEvaluacionController@quitarPregEvaluacion');
Route::post('evaluacionesDocente','EvaluacionController@evaluacionesDocente');
Route::post('getRespuestasGrupo','PregEvaluacionController@getRespuestasGrupo');
Route::get('getRespuestas/{id}','PregEvaluacionController@getRespuestas');
Route::post('getRespuestasAcum','PregEvaluacionController@getRespuestasAcum');
Route::post('evaluacionesEstudianteCurso','EvaluacionController@evaluacionesEstudianteCurso');
Route::post('evalCompleEstCurso','EvaluacionController@evalCompleEstCurso');
// Route::get('asignaturasDoc/{id}','AsignaturaController@asignaturasDoc');
Route::get('asignaturasCreaDoc/{id}','AsignaturaController@asignaturasCreaDoc');
Route::get('verCalificacionEval/{id}','EvaluacionController@verCalificacionEval');
Route::get('verEvalCursoExport/{id}','EvaluacionController@verEvalCursoExport');
Route::post('cargarOpcionDico','PreguntaController@cargarOpcionDico');
Route::post('preguntasxtema','PreguntaController@preguntasxtema');
Route::post('preguntastipo','PreguntaController@preguntastipo');
Route::post('preguntasxunidad','PreguntaController@preguntasxunidad');
Route::post('preguntasevaltipounidad','PreguntaController@preguntasevaltipounidad');
Route::get('eliminarPregunta/{id}','PreguntaController@eliminarPregunta');
Route::get('tipospreguntas/{asignatura}/{unidades}','PreguntaController@tipospreguntas');
Route::post('cargarPregsRand','PreguntaController@cargarPregsRand');
Route::get('verEstCursoEval/{id}','EvaluacionController@verEstCursoEval');
Route::post('asignarGrupoEst','EvaluacionController@asignarGrupoEst');
Route::get('tipoevaluacion', 'EvaluacionController@TiposEvaluacion');
Route::post('clasifGrupEstEval','PregEvaluacionController@clasifGrupEstEval');
Route::post('verRespEstudianteEval', 'PregEvaluacionController@verRespEstudianteEval');
Route::post('modificarEvaluacion', 'CalificacionEvalController@modificarEvaluacion');
Route::post('guardarRespuesta','CalificacionEvalController@guardarRespuesta');
Route::get('eliminar_evaluacion/{id}', 'EvaluacionController@eliminar_evaluacion');


Route::post('generarCodigos','CodigosLibrosGenerarController@generarCodigos');
//apis codigos libros
Route::apiResource('series', 'SeriesController');
//para codigos bloqueados
Route::get('codigosBloqueados','LibroSerieController@codigosBloqueados');
Route::apiResource('libros_series', 'Series_librosController');
Route::apiResource('codigosLibros', 'CodigosLibrosGenerarController');
Route::get('codigosLibrosFecha/{id}', 'CodigosLibrosGenerarController@codigosLibrosFecha');
Route::get('codigosLibrosExportados/{id}', 'CodigosLibrosGenerarController@codigosLibrosExportados');
Route::get('librosBuscar', 'CodigosLibrosGenerarController@librosBuscar');
Route::get('codigosLibrosCodigo/{id}','CodigosLibrosGenerarController@codigosLibrosCodigo');
Route::get('editarCodigoBuscado/{id}','CodigosLibrosGenerarController@editarCodigoBuscado');
Route::get('estudianteCodigo/{id}','EstudianteController@estudianteCodigo');
Route::get('cedulasEstudiantes/{id}','EstudianteController@cedulasEstudiantes');
Route::get('seriesCambiar','CodigosLibrosGenerarController@seriesCambiar');
Route::get('librosSerieCambiar/{id}','CodigosLibrosGenerarController@librosSerieCambiar');
Route::get('series_libros_doc/{id}','CursoController@series_libros_doc');
Route::get('ver_areas_serie/{id_serie}/{id_usuario}','CursoController@ver_areas_serie');
Route::get('get_libros_area/{usuario}/{area}/{serie}','CursoController@get_libros_area');
Route::get('librosCambiar/{id}','CodigosLibrosGenerarController@librosCambiar');
Route::post('reportesCodigoInst','CodigosLibrosGenerarController@reportesCodigoInst');

Route::post('editarInstEstud', 'CodigosLibrosGenerarController@editarInstEstud');
Route::get('reportesCodigoAsesor/{id}/{periodo}', 'CodigosLibrosGenerarController@reportesCodigoAsesor');
Route::get('institucionEstCod/{id}', 'EstudianteController@institucionEstCod');
///reportes
Route::get('nivelesInstitucion/{id}', 'NivelController@nivelesInstitucion');
Route::get('institucionUsuario/{id}', 'usuarioController@institucionUsuario');
Route::get('docentesInstitucion/{id}','DocenteController@docentesInstitucion');
Route::get('estudiantesInstitucion/{id}','EstudianteController@estudiantesInstitucion');
Route::get('reporteLibros','ReporteUsuarioController@index');
Route::get('docentes','UsuarioController@docentes');

// Estadisticas
Route::get('contenidos','EstadisticasController@contenidos');
//JUEGOS
Route::apiResource('j_juegos', 'J_juegosController');
Route::get('j_juegosTipos', 'J_juegosController@j_juegosTipos');
Route::apiResource('j_contenidos', 'J_contenidoController');
Route::apiResource('tipoJuegos', 'TipoJuegosController');
Route::get('unidadesAsignatura/{id}', 'TipoJuegosController@unidadesAsignatura');
Route::get('juego_y_contenido/{id}', 'J_juegosController@juego_y_contenido');
Route::post('j_juegos_tipo', 'J_juegosController@j_juegos_tipo');
Route::get('juegos_prolipa_admin_tipo/{id}', 'J_juegosController@juegos_prolipa_admin_tipo');
Route::post('j_juegos_tipo_prolipa', 'J_juegosController@j_juegos_tipo_prolipa');
Route::post('j_juegos_ficha', 'J_juegosController@j_juegos_ficha');
Route::post('guardarTemasJuego', 'J_juegosController@guardarTemasJuego');
Route::get('eliminarTemasJuego/{id}', 'J_juegosController@eliminarTemasJuego');
Route::get('j_juegos_eliminar/{id}', 'J_juegosController@j_juegos_eliminar');
Route::post('j_guardar_calificacion', 'J_juegosController@j_guardar_calificacion');
Route::post('calificacion_estudiante', 'J_juegosController@calificacion_estudiante');
Route::post('j_juegos_tipo_curso_doc', 'J_juegosController@j_juegos_tipo_curso_doc');
Route::post('cursos_jugaron', 'CursoController@cursos_jugaron');
Route::post('asignar_cursos_juego', 'J_juegosController@asignar_cursos_juego');
Route::get('juegos_has_curso/{id}', 'J_juegosController@juegos_has_curso');
Route::post('calificaciones_estudiante_juego', 'J_juegosController@calificaciones_estudiante_juego');
Route::get('juego_preguntas_opciones/{id}', 'J_juegosController@juego_preguntas_opciones');
//ACTIVIDADES - ANIMACIONES
Route::apiResource('registro_actividades', 'ActividadAnimacionController');
Route::get('asignaturasActi','ActividadAnimacionController@getAsignaturas');
Route::get('actividades_x_Tema/{id}', 'ActividadAnimacionController@actividades_x_Tema');
Route::get('eliminaActividad/{id}', 'ActividadAnimacionController@eliminaActividad');
Route::post('temasUnidad', 'ActividadAnimacionController@temasUnidad');
Route::get('temasUnidad_id/{id}', 'ActividadAnimacionController@temasUnidadID');
Route::get('actividadesBuscarFechas/{id}', 'ActividadAnimacionController@actividadesBuscarFechas');
Route::get('carpetaActividades/{id}', 'ActividadAnimacionController@carpetaActividades');
Route::get('actividades_x_Libro/{id}', 'ActividadAnimacionController@actividades_x_Libro');
Route::get('actividades_libros_unidad/{id}','ActividadAnimacionController@actividades_libros_unidad');
Route::get('actividades_libros_unidad_tema/{id}','ActividadAnimacionController@actividades_libros_unidad_tema');
Route::get('animaciones_libros_unidad/{id}','ActividadAnimacionController@animaciones_libros_unidad');
Route::get('animaciones_libros_unidad_tema/{id}','ActividadAnimacionController@animaciones_libros_unidad_tema');
//VERIFICAR CORREO RESTAURAR CONTRASEÑA
Route::post('verificarCorreo', 'UsuarioController@verificarCorreo');
///CURSOS ADMINISTRADOR
Route::get('buscarCursoCodigo/{id}', 'CursoController@buscarCursoCodigo');
Route::post('restaurarCurso/{id}', 'CursoController@restaurarCurso');
Route::get('cursos_x_usuario/{id}', 'CursoController@cursos_x_usuario');
Route::get('cursos_x_estudiante/{id}', 'CursoController@cursos_x_estudiante');
//PROMEDIO
Route::get('cursosInstitucion/{id}', 'ReporteUsuarioController@cursosInstitucion');
//ESTADISTICAS ADMINISTRADOR
Route::get('cant_user', 'AdminController@cant_user');
Route::get('cant_cursos', 'AdminController@cant_cursos');
Route::get('cant_codigos', 'AdminController@cant_codigos');
Route::get('cant_codigostotal', 'AdminController@cant_codigostotal');
Route::get('cant_evaluaciones', 'AdminController@cant_evaluaciones');
Route::get('cant_preguntas', 'AdminController@cant_preguntas');
Route::get('cant_multimedia', 'AdminController@cant_multimedia');
Route::get('cant_juegos', 'AdminController@cant_juegos');
Route::get('cant_seminarios', 'AdminController@cant_seminarios');
Route::get('cant_encuestas', 'AdminController@cant_encuestas');
//CANTIDAD EVALUACIONES PERFIL DOCENTE
Route::get('cant_evaluaciones/{id}', 'DocenteController@cant_evaluaciones');
//CANTIDAD DE ARCHIVOS DE UN DOCENTE
Route::get('cant_contenido/{id}', 'DocenteController@cant_contenido');
//RUTA DE ENCUESTAS
Route::get('encuesta_certificados/{id}', 'SeminarioController@encuesta_certificados');
Route::get('asistentes_seminario/{id}', 'SeminarioController@asistentes_seminario');
//UNIDADES
// Route::apiResource('unidadesLibros', 'UnidadController');
Route::get('libro_enUnidad', 'UnidadController@libro_enUnidad');
Route::get('unidadesX_Libro/{id}', 'UnidadController@unidadesX_Libro');
Route::post('updateUnidades', 'UnidadController@updateUnidades');
//MATERIAL DE APOYO EN ADMINISTRADOR
Route::get('todo_asignaturas', 'MaterialApoyoController@todo_asignaturas');
Route::get('todo_material_apoyo/{id}', 'MaterialApoyoController@todo_material_apoyo');
Route::get('materialapoyo_asignaturas', 'MaterialApoyoController@materialapoyo_asignaturas');
Route::post('quitar_material_asignatura', 'MaterialApoyoController@quitar_material_asignatura');
Route::post('agregar_material_asignaturas', 'MaterialApoyoController@agregar_material_asignaturas');
Route::post('editar_material_asignaturas', 'MaterialApoyoController@editar_material_asignaturas');
Route::get('material_estados', 'MaterialApoyoController@material_estados');
Route::post('registrar_material', 'MaterialApoyoController@registrar_material');
Route::post('eliminarMaterial', 'MaterialApoyoController@eliminarMaterial');
Route::get('showMaterial/{id}', 'MaterialApoyoController@showMaterial');
Route::post('temas_asignatura_material', 'MaterialApoyoController@temas_asignatura_material');
Route::post('temas_material', 'MaterialApoyoController@temas_material');
//API OBTENER TEMAS POR MATERIAL
Route::get('temas_por_material/{id}', 'MaterialApoyoController@temas_por_material');
// API MATERIASL APOYO DOCENTE
Route::post('calificaciones_material_curso', 'MaterialApoyoController@calificaciones_material_curso');
Route::post('material_curso', 'MaterialApoyoController@material_curso');
Route::post('asignar_cursos_material', 'MaterialApoyoController@asignar_cursos_material');
Route::post('material_curso_estudiante', 'MaterialApoyoController@material_curso_estudiante');
Route::post('guardar_material_usuario', 'MaterialApoyoController@guardar_material_usuario');
//BLOQUEAR - activar CODIGO LIBRO DESDE ADMINISTRADOR
Route::post('cambioEstadoCodigo', 'CodigosLibrosGenerarController@cambioEstadoCodigo');
//BORRAR TEMAS DE UN MATERIAL
Route::post('borrar_temas_material', 'MaterialApoyoController@borrar_temas_material');
Route::post('borrar_material_asig', 'MaterialApoyoController@borrar_material_asig');
Route::post('editar_material', 'MaterialApoyoController@editar_material');
//UNA ASIGNATURA PARA LOS PROYECTOS DEL DOCENTE
Route::get('asignaturaIdProyectos/{id}', 'ActividadAnimacionController@asignaturaIdProyectos');
//CURSOS POR DOCENTE POR ASIGNATURA SELECCIONADA Y PERIODO LECTIVO ACTIVO
Route::post('cursos_asignatura_docente', 'CursoController@cursos_asignatura_docente');
//PERIODO LECTIVO ACTIVO PARA REGISTRO DE INSTITUCIONES
Route::get('periodoActivo', 'PeriodoController@periodoActivo');
//AGREGAR CODIGO LIBRO PERDIDO
Route::post('agregar_codigo_perdido', 'CodigosLibrosGenerarController@agregar_codigo_perdido');
//TEMAS TELETAREAS
Route::get('temas','AsignaturaController@temas');
Route::get('asigTemas','AsignaturaController@asigTemas');
//LISTA DE ESTUDIANTES, para historico de visitas
Route::get('estudiantesXInstitucion/{id}','UsuarioController@estudiantesXInstitucion');
//HISTORICO LIBROS DE ESTUDIANTES
Route::get('getHistoricoCodigos/{id}','CodigosLibrosGenerarController@getHistoricoCodigos');
//INSTITUCIONES DIRECTOR
Route::get('institucionesDirector/{id}','PeriodoInstitucionController@institucionesDirector');
Route::post('guardarLogoInstitucion','InstitucionController@guardarLogoInstitucion');
// cargar periodo a codigo libro
Route::get('cargarPeriodoCodigo','CodigosLibrosGenerarController@cargarPeriodoCodigo');

//api para ver los usuarios por periodo
Route::get('UsuariosPeriodo','PeriodoController@UsuariosPeriodo');


//seminarios de un docente
Route::get('seminariosDocente/{id}','SeminarioController@seminariosDocente');
//salle
Route::apiResource('areas_salle','SalleAreasController');
Route::apiResource('asignaturas_salle','SalleAsignaturasController');
Route::get('asignaturas_area_salle/{id}','SalleAsignaturasController@asignaturas_area_salle');
Route::post('crea_area_salle','SalleAreasController@crea_area_salle');
Route::post('crea_asignatura_salle','SalleAsignaturasController@crea_asignatura_salle');
Route::get('instituciones_salle','InstitucionController@instituciones_salle');
Route::get('instituciones_salle_select','InstitucionController@instituciones_salle_select');
Route::post('save_instituciones_salle','InstitucionController@save_instituciones_salle');
// salle asignaturas docente
Route::get('asignaturas_docente_salle/{id}','SalleAsignaturasController@asignaturas_docente_salle');
Route::post('save_asignaturas_docente_salle','SalleAsignaturasController@save_asignaturas_docente_salle');
Route::get('delete_asignaturas_docente_salle/{id}','SalleAsignaturasController@delete_asignaturas_docente_salle');
Route::get('asignaturas_por_area_salle/{id}','SalleAsignaturasController@asignaturas_por_area_salle');
// preguntas salle
Route::apiResource('preguntas_salle','SallePreguntasController');
Route::get('opciones_pregunta_salle/{id}','SallePreguntasController@opciones_pregunta_salle');
Route::post('cargar_opcion_salle','SallePreguntasController@cargar_opcion_salle');
Route::post('editar_opcion_salle','SallePreguntasController@editar_opcion_salle');
Route::get('quitar_opcion_salle/{id}','SallePreguntasController@quitar_opcion_salle');
Route::get('eliminar_pregunta_salle/{id}','SallePreguntasController@eliminar_pregunta_salle');
Route::post('cargar_opcion_vf_salle','SallePreguntasController@cargar_opcion_vf_salle');
Route::post('transformar_preguntas_salle','SallePreguntasController@transformar_preguntas_salle');
Route::get('validar_puntajes','SallePreguntasController@validar_puntajes');
// evaluaciones salle
Route::get('generar_evaluacion_salle/{id_docente}/{id_institucion}','SallePreguntasController@generar_evaluacion_salle');
Route::get('salle_getConfiguracion/{id_institucion}','SallePreguntasController@salle_getConfiguracion');
Route::get('obtener_evaluacion_salle/{id_docente}/{id_evaluacion}','SallePreguntasController@obtener_evaluacion_salle');
Route::post('salle_finalizarEvaluacion','SallePreguntasController@salle_finalizarEvaluacion');
Route::get('evaluaciones_resueltas_salle/{id_docente}','SallePreguntasController@evaluaciones_resueltas_salle');
Route::get('reporte_evaluaciones_institucion/{fecha}','SallePreguntasController@reporte_evaluaciones_institucion');
Route::post('salle_guardarSeleccion','SallePreguntasController@salle_guardarSeleccion');
Route::post('salle_intento_eval','SallePreguntasController@salle_intento_eval');
//salle reportes
Route::get('reporte_evaluaciones_institucion/{fecha}','SalleReportesController@reporte_evaluaciones_institucion');
Route::get('salle_promedio_areas/{periodo}/{institucion}','SalleReportesController@salle_promedio_areas');
Route::get('salle_promedio_asignatura/{periodo}/{institucion}/{area}','SalleReportesController@salle_promedio_asignatura');
Route::get('salle_promedios_tipos_pregunta/{periodo}/{institucion}/{id_asignatura}','SalleReportesController@salle_promedios_tipos_pregunta');
//archivos departamentos
Route::resource('files_departamentos','FilesDepartamentosController');
Route::get('ver_archivos_departamento/{id_categoria}','FilesDepartamentosController@ver_archivos_departamento');
Route::get('archivos_departamento_filtro/{id_categoria}/{fecha}/{tipo}','FilesDepartamentosController@archivos_departamento_filtro');
Route::post('remover_archivo','FilesDepartamentosController@remover_archivo');

//lista menu
Route::get('grupos_users','MenuController@grupos_users');
Route::get('listaMenu','MenuController@listaMenu');
Route::post('add_editMenu','MenuController@add_editMenu');
Route::get('eliminarMenu/{id}','MenuController@eliminarMenu');
//para traer la cantidad de usuarios para mostrar en el home
Route::get('traerCantidadUsuarios','UsuarioController@traerCantidadUsuarios');
//usuarios salle
Route::get('usuarioSalle','UsuarioController@usuarioSalle');
Route::post('add_edit_user_salle','UsuarioController@add_edit_user_salle');
Route::post('activa_desactiva_user','UsuarioController@activa_desactiva_user');
Route::post('updatePassword','UsuarioController@cambiarPassword');
//instituciones Salle
Route::get('institucionesSalle','InstitucionController@institucionesSalle');
// Apis steven
Route::resource('libroserie','LibroSerieController');
//api para ver libro serie de un libro especifico
Route::get('verLibroSerie','LibroSerieController@verLibroSerie');
//activar o desativar la data de la tabla libro-serie
Route::post('libroserie/desactivar','LibroSerieController@desactivar');
Route::post('libroserie/activar','LibroSerieController@activar');
//apis  para la tabla  temporadas
Route::resource('temporadas','TemporadaController')->except(['edit','create']);

Route::get('/liquidacion/{contrato}','TemporadaController@liquidacionMilton');
Route::get('temporadas/liquidacion/{contrato}','TemporadaController@liquidacion');
//api para traer los contratos para que los asesores puedan visualizar
Route::post('temporadas/asesor/contratos','TemporadaController@asesorcontratos');
//api para eliminar como prueba
Route::post('temporadas/eliminar','TemporadaController@eliminarTemporada');
//activar o desativar la data de la tabla temporada
Route::post('temporadas/desactivar','TemporadaController@desactivar');
Route::post('temporadas/activar','TemporadaController@activar');
Route::post('temporadas/docente','TemporadaController@agregardocente');


Route::post('cambiar_periodo_curso','PeriodoInstitucionController@cambiar_periodo_curso');
//api para traer las instituciones por ciudad
Route::post('traerprofesor','TemporadaController@traerprofesores');
//api para mostrar  las liquidaciones para milton
// Route::get('showliquidacion/{contrato}','TemporadaController@showLiquidacion');

//Api para milton gel
  Route::get('temporada/datos','TemporadaController@temporadaDatos');
//api para subir la data a la base de datos en este caso a la tabla codigos libros
Route::resource('/subirdata','SubirdataController');
//api para subir material
Route::resource('cargarmaterial','MaterialcargarController');
//api para desactivar material
Route::post('cargarmaterial/desactivar','MaterialcargarController@desactivar');
//api para activar material
Route::post('cargarmaterial/activar','MaterialcargarController@activar');
//api para traer unidades material
Route::post('traerunidades','MaterialcargarController@traerunidades');
//api para traer temas material
Route::post('traertemas','MaterialcargarController@traertemas');
//api para eliminar archivos material
Route::post('archivoseliminar','MaterialcargarController@eliminar');
//api para traer archivos por asignatura
Route::get('traer_archivos_asignaturas/{asignatura}','MaterialcargarController@traer_archivos_asignaturas');
//api para asignar archivos al curso
Route::post('asignar_cursos_archivos','MaterialcargarController@asignar_cursos_archivos');
//api para traer los materiales del curso del estudiante
Route::post('archivo_curso','MaterialcargarController@archivo_curso');
//api para materiales unidades listado
Route::get('materialunidades/{id}','MaterialcargarController@materialunidades');
//api para materiales unidades editar
Route::post('materialunidadeseditar','MaterialcargarController@materialunidadeseditar');
//api para materiales unidades eliminar
Route::post('materialunidadeseliminar','MaterialcargarController@materialunidadeseliminar');
//api para materiales temas listado
Route::get('materialtemas/{id}','MaterialcargarController@materialtemas');
//api para materiales temas editar
Route::post('materialtemaseditar','MaterialcargarController@materialtemaseditar');
//api para materiales temas eliminar
Route::post('materialtemaseliminar','MaterialcargarController@materialtemaseliminar');
//api para gestion de liquidacion
Route::post('liquidacionperiodo','LibroSerieController@liquidacionperiodo');
//api para listado de instituciones para milton
Route::get('instituciones_facturacion','TemporadaController@instituciones_facturacion');
//api para  actualizar la institucion del asesor
Route::post('asesor-institucion','TemporadaController@asesorInstitucion');
//lista instituciones dato especificos
Route::get('listaInstitucionesActiva','InstitucionController@listaInstitucionesActiva');
//areas SIN basicas SALLE
Route::get('areasSinBasica','SalleAreasController@areasSinBasica');
Route::get('institucionConfiguracionSalle/{id}','InstitucionController@institucionConfiguracionSalle');
//Juego seleccionSimple
Route::get('pregunta_opciones/{id}', 'J_contenidoController@preguntas_y_opciones');
Route::post('saveSeleccion', 'J_contenidoController@guardaSeleccionSimple');
Route::post('deleteImagen', 'J_contenidoController@deleteImagenSeleccionSimple');
//lista instituciones todas
Route::get('listaInsitucion', 'InstitucionController@listaInsitucion');
//lista instituciones del asesor
Route::get('listaInsitucionAsesor', 'InstitucionController@listaInsitucionAsesor');
//historico codigos usados
Route::get('historico_codigo/{id}', 'CodigosLibrosGenerarController@hist_codigos');
//lista de asesores
Route::get('listaAsesores', 'UsuarioController@asesores');
//Rutas para areas
Route::resource('areas','AreaController');

//Fin rutas para colegios
Route::post('area-eliminar','AreaController@areaeliminar');
//periodos por instituciones
Route::get('periodosXInstitucion/{id}','PeriodoInstitucionController@periodosXInstitucion');
Route::post('verifica_periodo','PeriodoInstitucionController@verificaPeriodoInstitucion');
Route::get('eliminarPeriodosInstitucion/{id}','PeriodoInstitucionController@eliminarPeriodosXInstitucion');
//elimina areas de salle
Route::get('eliminaArea/{id}','SalleAreasController@eliminaArea');
//elimina asignaturas de salle
Route::get('eliminaAsignatura/{id}','SalleAsignaturasController@eliminaAsignatura');
//asignaturas por docente
Route::post('asignaturasDocent','AsignaturaDocenteController@asignaturas_x_docente');
Route::get('eliminaAsignacion/{id}','AsignaturaDocenteController@eliminaAsignacion');
Route::post('asignar_asignatura_docentes', 'AsignaturaDocenteController@asignar_asignatura_docentes');
//lista de libros por estudiante y periodo activo
Route::post('estudiantesLibros','EstudianteController@estudiantesLibros');
//agregar libro al estudiantes desde perfil director, asesor y administrador
Route::post('addLibroEstudianteDirector','EstudianteController@addLibroEstudianteDirector');
Route::post('quitaTodasAsignaturas','AsignaturaDocenteController@quitarTodasAsignaturasDocente');
//agregar user desde admin
Route::post('add_user_ad','UsuarioController@add_user_admin');
Route::get('usuarios_grupos','UsuarioController@user_por_grupo');
//traer directores
Route::get('getDirectores','UsuarioController@getDirectores');
//para ver institucion del director
Route::get('verInstitucionDirector','UsuarioController@verInstitucionDirector');
Route::get('cambiarDirector/{id}','UsuarioController@cambiarDirector');

 // agenda docente..
Route::get('get_agenda_docente/{id}','DocenteController@get_agenda_docente');
Route::post('save_agenda_docente','DocenteController@save_agenda_docente');
Route::get('delete_agenda_docente/{id}','DocenteController@delete_agenda_docente');
Route::get('modificar_periodo_codigos','SallePreguntasController@modificar_periodo_codigos');


//ingrsos masivos
Route::get('ingresos_masivos','UsuarioController@ingresos_masivos');

//capacitacion temas
Route::resource('capacitacionTema','CapacitacionTemaController');
Route::get('agenda_capacitacion/{id}','CapacitacionTemaController@getAgendaCapacitaciones');
Route::post('periodoActivoReg','PeriodoController@periodoActivoPorRegion');
Route::post('editar_agenda_adm','CapacitacionController@edit_agenda_admin');

