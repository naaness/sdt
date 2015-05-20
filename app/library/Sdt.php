<?php
/**
 * Created by PhpStorm.
 * User: nesto_000
 * Date: 18/02/15
 * Time: 04:27 PM
 */

use Phalcon\Mvc\User\Component;
use Phalcon\Mvc\Model\Query\Simple;

/**
 *
 * Main calculates to do logic sdt
 */
class Sdt extends Component
{
    /**
     * find range method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function findRange($range = "trimestre", $day = false, $month = false, $year = false)
    {

        if (!$day || !$month || !$year) { // Dia por defecto es hoy
            $fecha_hoy = new \DateTime('America/Mexico_City');
            $day = $fecha_hoy->format('d');
            $month = $fecha_hoy->format('m');
            $year = $fecha_hoy->format('Y');
        }
        $dates = array();
        if ($range == "trimestre") {
            if ($month < 4) {
                $dates['start'] = '01/01/' . $year;
                $dates['end'] = '01/04/' . $year;
                $dates['title'] = "Trimestre 1 " . $year;

                $dates['n_days'] = 'n';
            } else if ($month < 7) {
                $dates['start'] = '01/04/' . $year;
                $dates['end'] = '01/07/' . $year;
                $dates['title'] = "Trimestre 2 " . $year;
            } else if ($month < 10) {
                $dates['start'] = '01/07/' . $year;
                $dates['end'] = '01/10/' . $year;
                $dates['title'] = "Trimestre 3 " . $year;
            } else {
                $dates['start'] = '01/10/' . $year;
                $dates['end'] = '01/01/' . ($year + 1);
                $dates['title'] = "Trimestre 4 " . $year;
            }
        } else if ($range == "month") {
            $dates['start'] = '01/' . $month . '/' . $year;
            $dates['title'] = $this->stringMonths($month) . " " . $year;
            if ($month + 1 > 12) {
                $month = 0;
                $year += 1;
            }
            $dates['end'] = "01" . '/' . ($month + 1) . '/' . $year;
        } else if ($range == "week") {
            $numdia = date('w', mktime(0, 0, 0, $month, $day, $year));
            // 0,1,2,3,4,5,6
            if ($numdia == 0) {
                $dife1 = -6;
            } else {
                $dife1 = 1 - $numdia;
            }

            $fecha = $day . "/" . $month . "/" . $year;

            $dates['start'] = $this->_operateDate($fecha, $dife1);

            $dates['end'] = $this->_operateDate($dates['start'], 7);
            $fecha = explode('/', $dates['end']);
            $semana = date('W', mktime(0, 0, 0, $month, $day, $year));

            $dates['title'] = "Semana " . $semana . " " . $fecha[2];
        }
        $dates['diff_days'] = $this->diffDate($dates['end'], $dates['start']);
        return $dates;
    }

    /**
     * add or rest days to Date method
     *
     * @throws NotFoundException
     * @param string $date , int $days
     * @return date
     */
    public function _operateDate($date, $days)
    {
        if ($days != 0) {
            $date = explode('/', $date);
            return date('d/m/Y', mktime(0, 0, 0, $date[1], $date[0], $date[2]) + 60 * 60 * 24 * $days);
        } else {
            return $date;
        }
    }


//    public function diffDate($start_day, $end_day){
//        $date1 = explode('/',$start_day);
//        $date2 = explode('/',$end_day);
//        $date1 =  mktime(0, 0, 0, $date1[1], $date1[0], $date1[2]);
//        $date2 =  mktime(0, 0, 0, $date2[1], $date2[0], $date2[2]);
//        $interval =($date2 - $date1)/(3600*24);
//        return round($interval, 0, PHP_ROUND_HALF_UP);
//    }

    /**
     * getUnitTimesOfTasks method
     *
     * @throws NotFoundException
     * @param string $id
     * @return view
     */
    public function getUnitTimesOfTasks($date = 0, $range = 'month', $task_id = 0, $team_id = 0, $project_id = 0, $package_id = 0, $model = 0, $type = 'normal')
    {
        if ($date) {
            $date = explode('/', $date);
            $ranges = $this->findRange($range, $date[0], $date[1], $date[2]);
        } else {
            $ranges = $this->findRange($range);
        }

        $date1 = explode('/', $ranges['start']);
        $date2 = explode('/', $ranges['end']);

        $user_id = $this->session->get("userId");

        $this->layout = 'blank';
        if ($type == 'tasks') { // Traer las tareas que fueron delegadas a mi

            $phql = 'SELECT
			Tasks.*
			FROM Tasks
			LEFT JOIN UsersTasks ON UsersTasks.task_id = Tasks.id
			WHERE
			UsersTasks.user_id = ' . $user_id . '
			AND UsersTasks.status = 1
			AND Tasks.user_id = ' . $user_id . '
			AND Tasks.status > 0
            AND Tasks.status < 3';
            $tasks = $this->modelsManager->executeQuery($phql);

            return $this->processData($tasks, $ranges, $date1, $date2, false);

        } elseif ($type == 'pend') { // Traer las tareas tienen algo pendientes

            $phql = 'SELECT
			Tasks.*
			FROM Tasks
			WHERE
			Tasks.user_id = ' . $user_id . '
			AND Tasks.status > 0
            AND Tasks.status < 3';
            $tasks = $this->modelsManager->executeQuery($phql);
            $hoy = new \DateTime('America/Mexico_City');
            $hoy = $hoy->format('Y-m-d');
            return $this->processDataPend($tasks, $ranges, $date1, $date2, false, $hoy);

        } elseif ($type == 'tome') { // Traer las tareas que fueron delegadas a mi

//            $phql = 'SELECT
//			Tasks.*, UnidTimes.*
//			FROM UnidTimes
//			INNER JOIN Tasks ON Tasks.id = UnidTimes.task_id
//			INNER JOIN Delegates ON (Delegates.task_id = Tasks.id)
//			LEFT JOIN UsersTasks ON (UsersTasks.task_id = Tasks.id)
//			WHERE
//			Delegates.second_user = '.$user_id.'
//			AND UsersTasks.status = 1
//			AND Tasks.status > 0
//			AND UnidTimes.start_day BETWEEN "'.date('Y-m-d H:i:s', mktime(0, 0, 0, $date1[1], $date1[0], $date1[2])).'" AND
//			"'.date('Y-m-d H:i:s', mktime(0, 0, 0, $date2[1], $date2[0], $date2[2])).'"
//			ORDER BY Tasks.name ASC ';
//            $results = $this->modelsManager->executeQuery($phql);
//            return $this->processData1($results,$ranges, null);

            $phql = 'SELECT
			Tasks.*
			FROM Tasks
			INNER JOIN Delegates ON (Delegates.task_id = Tasks.id)
			WHERE
			Delegates.second_user = ' . $user_id . '
			AND Tasks.status > 0';
            $tasks = $this->modelsManager->executeQuery($phql);

            return $this->processData($tasks, $ranges, $date1, $date2, false);

        } elseif ($type == 'fromme') { // Traer las tareas que fueron delegadas por mi

//            $phql = 'SELECT
//			Tasks.*, UnidTimes.*
//			FROM UnidTimes
//			INNER JOIN Tasks ON Tasks.id = UnidTimes.task_id
//			INNER JOIN Delegates ON (Delegates.task_id = Tasks.id)
//			LEFT JOIN UsersTasks ON (UsersTasks.task_id = Tasks.id)
//			WHERE
//			Delegates.first_user = '.$user_id.'
//			AND UsersTasks.status = 1
//			AND Tasks.status > 0
//			AND UnidTimes.start_day BETWEEN "'.date('Y-m-d H:i:s', mktime(0, 0, 0, $date1[1], $date1[0], $date1[2])).'" AND
//			"'.date('Y-m-d H:i:s', mktime(0, 0, 0, $date2[1], $date2[0], $date2[2])).'"
//			ORDER BY Tasks.name ASC ';
//            $results = $this->modelsManager->executeQuery($phql);
//            return $this->processData1($results,$ranges, null);

            $phql = 'SELECT
			Tasks.*
			FROM Tasks
			INNER JOIN Delegates ON (Delegates.task_id = Tasks.id)
			LEFT JOIN Projects ON (Projects.id = Tasks.project_id)
			WHERE
			Delegates.first_user = ' . $user_id . '
			AND Tasks.status > 0';
            $tasks = $this->modelsManager->executeQuery($phql);

            return $this->processData($tasks, $ranges, $date1, $date2, false);

        } elseif ($type == 'mypro') { // Traer las tareas de los proyectos que cree

            $phql = 'SELECT
			Tasks.*
			FROM Tasks
			LEFT JOIN Projects ON (Projects.id = Tasks.project_id)
			WHERE
			Projects.user_id = ' . $user_id . '
			AND Tasks.status > 0';
            $tasks = $this->modelsManager->executeQuery($phql);

            return $this->processData($tasks, $ranges, $date1, $date2, false);
        } elseif ($type == 'proje') { // Traer las tareas de los proyectos que estoy involucrado

//            $phql = 'SELECT
//			Tasks.*, UnidTimes.*
//			FROM UnidTimes
//			INNER JOIN Tasks ON (Tasks.id = UnidTimes.task_id)
//			LEFT JOIN Projects ON Projects.id = Tasks.project_id
//			LEFT JOIN UsersProjects ON UsersProjects.project_id = Projects.id
//			LEFT JOIN UsersTasks ON (UsersTasks.task_id = Tasks.id)
//			WHERE
//			UsersProjects.user_id = '.$user_id.'
//			AND UsersTasks.status = 1
//			AND Tasks.status > 0
//			AND UnidTimes.start_day BETWEEN "'.date('Y-m-d H:i:s', mktime(0, 0, 0, $date1[1], $date1[0], $date1[2])).'" AND
//			"'.date('Y-m-d H:i:s', mktime(0, 0, 0, $date2[1], $date2[0], $date2[2])).'"
//			ORDER BY Tasks.name ASC ';
//            $results = $this->modelsManager->executeQuery($phql);
//            return $this->processData1($results,$ranges, null);

            $phql = 'SELECT
			Tasks.*
			FROM Tasks
			LEFT JOIN Projects ON (Projects.id = Tasks.project_id)
			INNER JOIN UsersProjects ON UsersProjects.project_id = Projects.id
			WHERE
			UsersProjects.user_id = ' . $user_id . '
			AND Tasks.status > 0';
            $tasks = $this->modelsManager->executeQuery($phql);

            return $this->processData($tasks, $ranges, $date1, $date2, false);

        } elseif ($package_id) {

            $phql = 'SELECT
			Tasks.*
			FROM Tasks
			WHERE
			Tasks.package_id = ' . $package_id . '
			AND Tasks.status > 0
			AND Tasks.task_id_parent = 0';
            $tasks = $this->modelsManager->executeQuery($phql);

            return $this->processData($tasks, $ranges, $date1, $date2, true);

        } elseif ($project_id) {

            $phql = 'SELECT
			Tasks.*
			FROM Tasks
			WHERE
			Tasks.project_id = ' . $project_id . '
			AND Tasks.status > 0';
            $tasks = $this->modelsManager->executeQuery($phql);

            return $this->processData($tasks, $ranges, $date1, $date2, true);
        } elseif ($team_id) {
            $model = ClassRegistry::init('Project');
            $options = array(
                'conditions' => array(
                    'Project.team_id' => $team_id,
                    "Tasks.status > 0",
                    'UnidTime.start_day >=' => date('Y-m-d H:i:s', mktime(0, 0, 0, $date1[1], $date1[0], $date1[2])),
                    'UnidTime.start_day <=' => date('Y-m-d H:i:s', mktime(0, 0, 0, $date2[1], $date2[0], $date2[2])),
                )
            );
            // Ajustar mejor esto para ver todas las tareas de que estan en el equipo
            $results = $model->find('all', $options);
            return $this->processData1($results, $ranges, null);
        } else {
            $phql = 'SELECT
			Tasks.*
			FROM Tasks
			INNER JOIN UsersTasks ON UsersTasks.task_id = Tasks.id
			WHERE
			UsersTasks.user_id = ' . $user_id . '
			AND UsersTasks.status = 1
			AND Tasks.status > 0
            AND Tasks.status < 3';
            $tasks = $this->modelsManager->executeQuery($phql);

            return $this->processData($tasks, $ranges, $date1, $date2);
        }
    }

    public function processData($tasks, $ranges, $date1, $date2, $view = false)
    {
        $user_id = $this->session->get("userId");
        $cont = -1;
        $data = array();
        $data["ranges"] = $ranges;
        $data["user_id"] = $user_id;
        $tar = array();
        foreach ($tasks as $task) {
            $unidTtimes = $task->getUnidTimes('start_day BETWEEN "' . date('Y-m-d H:i:s', mktime(0, 0, 0, $date1[1], $date1[0], $date1[2])) . '" AND "' . date('Y-m-d H:i:s', mktime(0, 0, 0, $date2[1], $date2[0], $date2[2])) . '"');
            $taskRepeats = $task->getTasksRepeats('start_day <= "' . date('Y-m-d', mktime(0, 0, 0, $date2[1], $date2[0], $date2[2])) . '" AND end_day >= "' . date('Y-m-d', mktime(0, 0, 0, $date1[1], $date1[0], $date1[2])) . '"')->toArray();
            if ($unidTtimes->count() > 0 Or count($taskRepeats) > 0 Or $view) {
                $cont += 1;
                $tar[$cont] = array(
                    'id' => $task->id,
                    'name' => $task->name,
                    'user_id' => $task->user_id,
                    'status' => $task->status,
                    'percent' => $task->percent,
                    'priority_id' => $task->priority_id,
                    'msgs' => $task->tasksMessages->count()
                );
                foreach ($unidTtimes as $unidTtime) {
                    $day = substr($unidTtime->start_day, 0, 10);
                    $tar[$cont]["unidTimes"][$day][] = array(
                        'id' => $unidTtime->id,
                        'f_up' => $unidTtime->follow_up,
                    );
                }

                $tar[$cont]["tasksRepeat"] = $taskRepeats;
                if ($task->project_id) {
                    $tar[$cont]['project'] = array(
                        "user_id" => $task->projects->user_id,
                        "code" => $task->projects->code
                    );
                }
                if ($task->package_id) {
                    $tar[$cont]['package'] = array(
                        "user_id" => $task->packages->user_id,
                        "code" => $task->packages->code
                    );
                }
                foreach ($task->rmRegistriesTasks as $rmRegistriesTask) {
                    if ($rmRegistriesTask->rmRegistries->rm_label_id > 0 and $rmRegistriesTask->rmRegistries->user_id == $user_id) {
                        $tar[$cont]['rm'] = array(
                            "c" => $rmRegistriesTask->rmRegistries->rmLabels->color,
                            "b_c" => $rmRegistriesTask->rmRegistries->rmLabels->b_color,
                            "f" => $rmRegistriesTask->rmRegistries->rmLabels->rmFonts->name
                        );
                    }
                }
            }
        }
        $data["tasks"] = $tar;
        return $data;
    }

    public function processDataPend($tasks, $ranges, $date1, $date2, $view = false, $date)
    {
        $user_id = $this->session->get("userId");
        $cont = -1;
        $data = array();
        $data["ranges"] = $ranges;
        $data["user_id"] = $user_id;
        $tar = array();
        foreach ($tasks as $task) {
            $unidTtimes = $task->getUnidTimes('start_day <= "' . $date . '" AND start_day != "0000-00-00" AND follow_up = 1');
            $taskRepeats = null; //$task->getTasksRepeats('start_day <= "'.date('Y-m-d', mktime(0, 0, 0, $date2[1], $date2[0], $date2[2])).'" AND end_day >= "'.date('Y-m-d', mktime(0, 0, 0, $date1[1], $date1[0], $date1[2])).'"')->toArray();
            if ($unidTtimes->count() > 0 Or count($taskRepeats) > 0 Or $view) {
                $cont += 1;
                $tar[$cont] = array(
                    'id' => $task->id,
                    'name' => $task->name,
                    'user_id' => $task->user_id,
                    'status' => $task->status,
                    'percent' => $task->percent,
                    'priority_id' => $task->priority_id,
                    'msgs' => $task->tasksMessages->count()
                );
                foreach ($unidTtimes as $unidTtime) {
                    $day = substr($unidTtime->start_day, 0, 10);
                    $tar[$cont]["unidTimes"][$day] = array(
                        'id' => $unidTtime->id,
                        'f_up' => $unidTtime->follow_up,
                    );
                }

                $tar[$cont]["tasksRepeat"] = $taskRepeats;
                if ($task->project_id) {
                    $tar[$cont]['project'] = array(
                        "user_id" => $task->projects->user_id,
                        "code" => $task->projects->code
                    );
                }
                if ($task->package_id) {
                    $tar[$cont]['package'] = array(
                        "user_id" => $task->packages->user_id,
                        "code" => $task->packages->code
                    );
                }
                foreach ($task->rmRegistriesTasks as $rmRegistriesTask) {
                    if ($rmRegistriesTask->rmRegistries->rm_label_id > 0 and $rmRegistriesTask->rmRegistries->user_id == $user_id) {
                        $tar[$cont]['rm'] = array(
                            "c" => $rmRegistriesTask->rmRegistries->rmLabels->color,
                            "b_c" => $rmRegistriesTask->rmRegistries->rmLabels->b_color,
                            "f" => $rmRegistriesTask->rmRegistries->rmLabels->rmFonts->name
                        );
                    }
                }
            }
        }
        $data["tasks"] = $tar;
        return $data;
    }

    public function processData1Old($results, $ranges, $resultsX2)
    {
        // Crear la matriz de filas como tareas y columnas como unidad de tiempo
        // Primer ciclo es para encontrar las diferentes tareas
        $datos = array();
        $user_id = $this->session->get("userId");

        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('d/m/Y');
        $segui = $this->icons();
        $state_task = $this->stateTask();

        $cont = 0;
        $almosOneEditable = false;
        foreach ($results as $result) {
            $cont += 1;
            if (!isset($datos[$result->tasks->id])) {
                //Saber si es lider de algun proyecto
                $editable = "editable-not";
                $code = '';
                $leader_project = false;
                if ($result->tasks->project_id > 0) {
                    $code = $result->tasks->projects->code;
                    if ($result->tasks->projects->user_id == $user_id) {
                        $leader_project = true;
                        $editable = "editable-yes";
                        $almosOneEditable = true;
                    }
                } elseif ($result->tasks->user_id != $user_id) {
                    $leader_project = true;
                }
                if ($result->tasks->project_id == 0 && $result->tasks->package_id > 0) {
                    $code = $result->tasks->packages->code;
                    if ($result->tasks->packages->user_id == $user_id && $result->tasks->task_id_parent == 0) {
                        $editable = "editable-yes";
                        $almosOneEditable = true;
                    }
                } elseif ($result->tasks->project_id == 0 && $result->tasks->user_id == $user_id) {
                    $editable = "editable-yes";
                    $almosOneEditable = true;
                }
                // estilo de la etiqueta, si lo tiene
                $style = 'style="background-color:#FFFFFF;"';
                foreach ($result->tasks->rmRegistriesTasks as $rmRegistriesTask) {
                    if ($rmRegistriesTask->rmRegistries->rm_label_id > 0 and $rmRegistriesTask->rmRegistries->user_id == $user_id) {
                        $style = 'style="color:' . $rmRegistriesTask->rmRegistries->rmLabels->color;
                        $style .= ';background-color:' . $rmRegistriesTask->rmRegistries->rmLabels->b_color;
                        $style .= ';font-family:' . $rmRegistriesTask->rmRegistries->rmLabels->rmFonts->name . '"';
                    }
                }
                // obtener las unidades repetitivas segun la tarea
                $datos[$result->tasks->id] = array(
                    'info' => $result->tasks->toArray(),
                    'code' => $code,
                    'are_you_leader' => $leader_project,
                    'class_state' => $state_task[$result->tasks->status],
                    'class_editable' => $editable,
                    'style' => $style
                );
            }

            $day = $this->formatDateSdt($result->unidTimes->start_day);
            $color_class = 'past';
            if ($this->diffDate($day, $today) >= 0) {
                $color_class = 'future';
            }
            $_edit = false;
            if ($result->tasks->user_id == $user_id) {
                $_edit = true;
            }
            $var1 = $result->unidTimes->follow_up;
            $datos[$result->tasks->id][$day] = array(
                'id' => $result->unidTimes->id,
                'editable' => $_edit,
                'aaaa' => $var1,
                'class-icon' => $segui['icon'][$var1],
                'class-color' => $segui['class'][$color_class][$var1],
                'follow_up' => $var1
            );
        }
        $mainData = array(
            'header' => $ranges,
            'maindata' => $datos,
            'mainrepeat' => $resultsX2->toArray(),
            'editable' => $almosOneEditable
        );
        return $mainData;
    }

    /**
     * icons method
     *
     * @throws NotFoundException
     * @param void
     * @return array
     */
    public function icons()
    {
        $segui = array(
            'icon' => array(
                1 => 'glyphicon glyphicon-minus',
                2 => 'glyphicon glyphicon-ok',
                3 => 'glyphicon glyphicon-remove',
                4 => 'glyphicon glyphicon-arrow-right'
            ),
            'class' => array(
                'future' => array(
                    1 => 'vacio',
                    2 => 'chuleado',
                    3 => 'nohizo',
                    4 => 'transferido',
                ),
                'past' => array(
                    1 => 'vaciolight',
                    2 => 'chuleadolight',
                    3 => 'nohizolight',
                    4 => 'transferidolight',
                )
            )
        );
        return $segui;
    }

    public function processData2($results, $ranges)
    {
        // Crear la matriz de filas como tareas y columnas como unidad de tiempo
        // Primer ciclo es para encontrar las diferentes tareas
        $datos = array();
        $user_id = $this->session->get("userId");
        $fecha_hoy = new \DateTime('America/Mexico_City');
        $today = $fecha_hoy->format('d/m/Y');
        $segui = $this->icons();
        $state_task = $this->stateTask();
        $editable = false;
        if (count($results) > 0) {
            $editable = true;
        }
        foreach ($results as $result) {
            if (!isset($datos[$result->tasks->id])) {
                $code = '';
                $leader_project = false;
                if ($result->tasks->project_id > 0) {
                    $code = $result->tasks->projects->code;
                    if ($result->tasks->projects->user_id == $user_id) {
                        $leader_project = true;
                    } else {
                        $editable = false;
                    }
                } elseif ($result->tasks->user_id != $user_id) {
                    $leader_project = true;
                }
                if ($result->tasks->package_id > 0) {
                    $code = $result->tasks->packages->code;
                }
                $datos[$result->tasks->id] = array(
                    'info' => $result->tasks->toArray(),
                    'code' => $code,
                    'are_you_leader' => $leader_project,
                    'class_state' => $state_task[$result->tasks->status]
                );
            }
            $day = $this->formatDateSdt($result->unidTimes->start_day);
            $color_class = 'past';
            if ($this->diffDate($today, $day) >= 0) {
                $color_class = 'future';
            }
            $_edit = false;
            if ($result->tasks->user_id == $user_id) {
                $_edit = true;
            }
            $datos[$result->tasks->id][$day] = array(
                'id' => $result->unidTimes->id,
                'editable' => $_edit,
                'class-icon' => $segui['icon'][$result->unidTimes->follow_up],
                'class-color' => $segui['class'][$color_class][$result->unidTimes->follow_up],
                'follow_up' => $result->unidTimes->follow_up
            );
        }

        $mainData = array(
            'header' => $ranges,
            'maindata' => $datos,
            'editable' => $editable
        );
        return $mainData;
    }


    /**
     * icons method
     *
     * @throws NotFoundException
     * @param void
     * @return array
     */
    public function stateTask()
    {
        $state = array(
            1 => 'task-accepted',
            2 => 'task-wait',
            3 => 'task-rejected',
        );
        return $state;
    }

    public function getTasksHtd($day0)
    {
        $day = explode('/', $day0);
        $day0 = $this->sdt->toStandardTime($day0);
        $user_id = $this->session->get("userId");

        $phql = 'SELECT
			Tasks.*, UnidTimes.*, Projects.*, Packages.*, RmRegistriesTasks.*, RmRegistries.rm_label_id
			FROM UnidTimes
			LEFT JOIN Tasks ON Tasks.id = UnidTimes.task_id
			INNER JOIN UsersTasks ON UsersTasks.task_id = Tasks.id
			LEFT JOIN Projects ON Projects.id = Tasks.project_id
			LEFT JOIN Packages ON Packages.id = Tasks.package_id
			LEFT JOIN RmRegistriesTasks ON RmRegistriesTasks.task_id = Tasks.id
			LEFT JOIN RmRegistries ON RmRegistriesTasks.rm_registry_id = RmRegistries.id
			WHERE
			UsersTasks.user_id = ' . $user_id . '
			AND UsersTasks.status = 1
			AND DAY(UnidTimes.start_day) = "' . $day[0] . '"
			AND MONTH(UnidTimes.start_day) = "' . $day[1] . '"
			AND YEAR(UnidTimes.start_day) = "' . $day[2] . '"';
        $tasks = $this->modelsManager->executeQuery($phql);

        //Ajustar mejor esto para ver todas las tareas de que estan en el equipo
        $phql = 'SELECT
			UsersTeams.*, IFNULL( CONCAT(Profiles.name," ", Profiles.last_name), Users.username) as username
			FROM Teams
			LEFT JOIN UsersTeams ON Teams.id = UsersTeams.team_id
			LEFT JOIN Users ON Users.id = UsersTeams.user_id
			LEFT JOIN Profiles ON Profiles.user_id = Users.id
			ORDER BY Users.username';
        $teams = $this->modelsManager->executeQuery($phql);

        $phql = 'SELECT
			RmLabels.*, RmFonts.*, RmSizes.*
			FROM RmLabels
			LEFT JOIN RmFonts ON RmFonts.id = RmLabels.rm_font_id
			LEFT JOIN RmSizes ON RmSizes.id = RmLabels.rm_size_id
			WHERE RmLabels.user_id = ' . $user_id;
        $labels = $this->modelsManager->executeQuery($phql);

        $phql = 'SELECT
			TasksRepeats.*, Tasks.*, Projects.*, Packages.*, RmRegistriesTasks.*, RmRegistries.rm_label_id
			FROM TasksRepeats
			LEFT JOIN Tasks ON Tasks.id = TasksRepeats.task_id
			INNER JOIN UsersTasks ON UsersTasks.task_id = Tasks.id
			LEFT JOIN Projects ON Projects.id = Tasks.project_id
			LEFT JOIN Packages ON Packages.id = Tasks.package_id
			LEFT JOIN RmRegistriesTasks ON RmRegistriesTasks.task_id = Tasks.id
			LEFT JOIN RmRegistries ON RmRegistriesTasks.rm_registry_id = RmRegistries.id
			WHERE
			UsersTasks.user_id = ' . $user_id . '
			AND UsersTasks.status = 1
			AND TasksRepeats.start_day <= "' . $day0 . '"
			AND TasksRepeats.end_day >= "' . $day0 . '"';
        $tasksrepeats = $this->modelsManager->executeQuery($phql);

        $datos = array(
            'tasks' => $tasks->toArray(),
            'teams' => $teams->toArray(),
            'user' => $user_id,
            'labels' => $labels->toArray(),
            'tasksrepeats' => $tasksrepeats->toArray()
        );
        return $datos;
    }

    public function formatDateSdt($date)
    {
        $day = explode(' ', $date);
        $day = explode('-', $day[0]);
        return $day[2] . '/' . $day[1] . '/' . $day[0];
    }

    public function diffDate($start_day, $end_day)
    {
        $interval = ($this->toMktime($start_day) - $this->toMktime($end_day)) / (3600 * 24);
        return round($interval, 0, PHP_ROUND_HALF_UP);
    }

    public function getDaysDiff($mktime)
    {
        return floor($mktime / (3600 * 24));
    }

    public function getHoursDiff($mktime)
    {
        return floor($mktime / (3600));
    }

    public function getMinutesDiff($mktime)
    {
        return floor($mktime / 60);
    }


    public function toMktime($date)
    {
        if ($date != "") {
            if (strpos($date, '/') > 0) {
                $date = explode('/', $date);
            } elseif (strpos($date, '-') > 0) {
                $date = $this->reorderDate($date);
            }
            return mktime(0, 0, 0, $date[1], $date[0], $date[2]);
        } else {
            return "";
        }
    }

    public function toStandardTime($date)
    {
        if ($date != "") {
            if (strpos($date, '/') > 0) {
                $date = explode('/', $date);
            } elseif (strpos($date, '-') > 0) {
                $date = $this->reorderDate($date);
            }
            return $date[2] . '-' . $date[1] . '-' . $date[0] . ' 00:00:00';
        } else {
            return "";
        }

    }

    public function reorderDate($date)
    {
        $date = explode(' ', $date);
        $date = explode('-', $date[0]);
        $arr = array();
        $arr[0] = $date[2]; // dia
        $arr[1] = $date[1]; // mes
        $arr[2] = $date[0]; // año
        return $arr;
    }

    public function diffName($diff = 0)
    {
        $name = "Hoy";
        $diff = (int)$diff;
        if ($diff == -1) {
            $name = "Ayer";
        } elseif ($diff == -2) {
            $name = "Antier";
        } elseif ($diff < -2 && $diff > -7) {
            $name = "Hace " . abs($diff) . " dias";
        } elseif ($diff < -7 && $diff > -29) {
            $interval = round(abs($diff) / 7, 0, PHP_ROUND_HALF_UP);
            $name = "Hace " . $interval . " semanas";
        } elseif ($diff < -29 && $diff > -365) {
            $interval = round(abs($diff) / 30, 0, PHP_ROUND_HALF_UP);
            $name = "Hace " . $interval . " meses";
        } elseif ($diff < -365) {
            $interval = round(abs($diff) / 365, 0, PHP_ROUND_HALF_UP);
            $name = "Hace " . $interval . " años";
        }
        return $name;
    }

    public function dateString($date)
    {
        if (strpos($date, '/') > 0) {
            $date = explode('/', $date);
        } elseif (strpos($date, '-') > 0) {
            $date = $this->reorderDate($date);
        }
        $date[0] = (int)$date[0];
        return $date[0] . ' ' . $this->stringMonths($date[1]) . ' ' . $date[2];
    }

    public function stringMonths($month)
    {
        $mesD = $this->nameMonths();
        return $mesD[$month];
    }

    public function nameMonths() {
        $mesD = array();
        $mesD["01"] = "Enero";
        $mesD["02"] = "Febrero";
        $mesD["03"] = "Marzo";
        $mesD["04"] = "Abril";
        $mesD["05"] = "Mayo";
        $mesD["06"] = "Junio";
        $mesD["07"] = "Julio";
        $mesD["08"] = "Agosto";
        $mesD["09"] = "Septiembre";
        $mesD["10"] = "Octubre";
        $mesD["11"] = "Noviembre";
        $mesD["12"] = "Diciembre";
        return $mesD;
    }

    public function dateStringSmart($date)
    {
        if (strpos($date, '/') > 0) {
            $date = explode('/', $date);
        } elseif (strpos($date, '-') > 0) {
            $date = $this->reorderDate($date);
        }
        $date[0] = (int)$date[0];
        $numdia = date('w', mktime(0, 0, 0, $date[1], $date[0], $date[2]));
        return $this->stringDay($numdia) . ' '. $date[0] .' de '. $this->stringMonths($date[1]) . ' del ' . $date[2];
    }

    public function stringDay($day){
        $dayD = $this->nameDays();
        return $dayD[$day];
    }
    public function nameDays() {
        $dayD = array();
        $dayD['0'] = "Lunes";
        $dayD['1'] = "Martes";
        $dayD['2'] = "Miercoles";
        $dayD['3'] = "Jueves";
        $dayD['4'] = "Viernes";
        $dayD['5'] = "Sabado";
        $dayD['6'] = "Domingo";
        return $dayD;
    }


    public function fixDelegate()
    {
        // obtener otas las tareas
        $tasks = Tasks::find();
        foreach ($tasks as $task) {
            $first_user = 0;
            foreach ($task->usersTasks as $usersTask) {
                if ($first_user > 0 && $usersTask->user_id > 0) {
                    $delegate = new Delegates();
                    $delegate->first_user = $first_user;
                    $delegate->second_user = $usersTask->user_id;
                    $delegate->task_id = $usersTask->task_id;
                    $delegate->save();
                    //echo($first_user . ' '  . $usersTask->user_id . ' '  . $usersTask->task_id . '</br>');
                }
                $first_user = $usersTask->user_id;
            }
        }
        return $this->response->redirect('index');
        //exit();
    }

    public function getChatTask($task_id)
    {
        $phql = 'SELECT
			TasksMessages.*, Users.username, Profiles.name, Profiles.last_name
			FROM TasksMessages
			LEFT JOIN Users ON Users.id = TasksMessages.user_id
			LEFT JOIN Profiles ON Profiles.user_id = TasksMessages.user_id
			WHERE
			TasksMessages.task_id = ' . $task_id . '
			ORDER BY TasksMessages.date DESC
			LIMIT 6';
        $results = $this->modelsManager->executeQuery($phql);
        $arr = array();
        $arr['user_id'] = $this->session->get("userId");
        $arr['messages'] = $results->toArray();
        $hoy = new \DateTime('America/Mexico_City');
        $arr['dateNow'] = $hoy->format('Y-m-d H:m:s');
        $task = Tasks::findFirst($task_id);
        $arr['taskname'] = $task->name;
        // actualizar mensaje visto
        $user_id = $this->session->get("userId");
        $userstasks = UsersTasks::find('user_id = ' . $user_id . ' AND task_id = ' . $task_id . ' AND status = 1');
        foreach ($userstasks as $usertask) {
            $usertask->new_message = 0;
            $usertask->save();
        }
        return $arr;
    }

    public function getChatTaskScroll($task_id, $last_range)
    {
        $phql = 'SELECT
			TasksMessages.*, Users.username, Profiles.name, Profiles.last_name
			FROM TasksMessages
			LEFT JOIN Users ON Users.id = TasksMessages.user_id
			LEFT JOIN Profiles ON Profiles.user_id = TasksMessages.user_id
			WHERE
			TasksMessages.task_id = ' . $task_id . '
			ORDER BY TasksMessages.date DESC
			LIMIT ' . ($last_range * 6) . ',6';
        $results = $this->modelsManager->executeQuery($phql);
        $arr = array();
        $arr['user_id'] = $this->session->get("userId");
        $arr['messages'] = $results->toArray();
        $hoy = new \DateTime('America/Mexico_City');
        $arr['dateNow'] = $hoy->format('Y-m-d H:m:s');
        return $arr;
    }

    public function getBasicChat()
    {
        $arr = array();
        $arr['user_id'] = $this->session->get("userId");
        $user = Users::findFirst($arr['user_id']);
        $username = " " . $user->username;
        if (!empty($user->profiles)) {
            $username = " " . $user->profiles->name . ' ' . $user->profiles->last_name;
        }
        $arr['name'] = $username;
        $hoy = new \DateTime('America/Mexico_City');
        $arr['dateNow'] = $hoy->format('Y-m-d H:m:s');
        return $arr;
    }

    public function createNotificationMessageTask($task_id)
    {
        $user_id = $this->session->get("userId");
        $users = UsersTasks::find('task_id =' . $task_id . ' AND status > 0');
        $hoy = new \DateTime('America/Mexico_City');
        $today = $hoy->format('Y-m-d');
        foreach ($users as $user) {
            if ($user->user_id != $user_id) {
                $notication = new Notifications();
                $notication->user_id = $user->user_id;
                $notication->change_user_id = $user_id;
                $notication->type = "messageTask";
                $notication->change_id = $user->task_id;
                $notication->date = $today;
                $notication->was_seen = 0;
                $notication->send_email = 0;
                $notication->save();

                $user->new_message = 1;
                $user->save();
            }
        }
    }

    public function getDaysTasksMonth($day)
    {
        $date = explode('/', $day);
        $date = $this->findRange("month", $date[0], $date[1], $date[2]);
        $date1 = explode('/', $date['start']);
        $date2 = explode('/', $date['end']);
        $user_id = $this->session->get("userId");

        $phql = 'SELECT
			UnidTimes.*
			FROM UnidTimes
			LEFT JOIN Tasks ON Tasks.id = UnidTimes.task_id
			INNER JOIN UsersTasks ON UsersTasks.task_id = Tasks.id
			WHERE
			UsersTasks.user_id = ' . $user_id . '
			AND UsersTasks.status = 1
			AND UnidTimes.start_day BETWEEN "' . date('Y-m-d H:i:s', mktime(0, 0, 0, $date1[1], $date1[0], $date1[2])) . '" AND
			"' . date('Y-m-d H:i:s', mktime(0, 0, 0, $date2[1], $date2[0], $date2[2])) . '"';
        $datos = $this->modelsManager->executeQuery($phql);

        $days = array();
        $days['days'] = array();
        foreach ($datos as $dato) {
            $dia = substr($dato->start_day, 8, 2);
            if (!isset($days['days'][$dia])) {
                $days['days'][$dia] = true;
            }
            if ($dato->follow_up == 1) {
                $days['days'][$dia] = false;
            }
        }
        return $days;
    }

    public function createAlert($utm, $id_unit_time, $type, $task)
    {
        if (!isset($task)) {
            $task = Tasks::findFirst($utm->task_id);
        }
        $create = true;
        if ($task->package_id > 0 && $task->task_id_parent == 0) {
            $create = false;
        }
        if ($create) {
            $alert = new Alerts();
            $alert->user_id = $task->user_id;
            $alert->change_user_id = $id_unit_time;
            $alert->unid_time_id = $utm->id;
            $alert->change_id = $task->id;
            $alert->type = "newActivity";
            $alert->date = $utm->start_day;
            $alert->save();
        }
    }

    public function createNotification($user_id, $change_user_id, $type, $change_id, $today)
    {
        $alert = new Notifications();
        $alert->user_id = $user_id;
        $alert->change_user_id = $change_user_id;
        $alert->type = $type;
        $alert->change_id = $change_id;
        $alert->date = $today;
        if ($alert->save() == false) {
            $html = '<ul>';
            foreach ($alert->getMessages() as $message) {
                $html .= '<li>' . $message . '</li>';

            }
            $html .= '</ul>';
            $this->flash->error($html);
        }
    }

    public function toDateStandard($date)
    {
        if ($date != "") {
            if (strpos($date, '/') > 0) {
                $date = explode('/', $date);
            } elseif (strpos($date, '-') > 0) {
                $date = $this->reorderDate($date);
            }
            return $date[2] . '-' . $date[1] . '-' . $date[0];
        } else {
            return "";
        }
    }

    public function getNewsMessagesTask()
    {
        $user_id = $this->session->get("userId");
        $phql = 'SELECT
			UsersTasks.task_id
			FROM UsersTasks
			WHERE
			UsersTasks.user_id = ' . $user_id . '
			AND UsersTasks.status = 1
			AND UsersTasks.new_message = 1';
        $userstasks = $this->modelsManager->executeQuery($phql);
        return $userstasks->toArray();
    }
}