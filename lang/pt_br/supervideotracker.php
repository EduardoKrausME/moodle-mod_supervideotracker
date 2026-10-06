<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Brazilian Portuguese language strings for Super Video Tracker.
 *
 * @package   mod_supervideotracker
 * @copyright 2026 Eduardo Kraus
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

$string['actions'] = 'Ações';
$string['active'] = 'Ativo';
$string['addvideo'] = 'Adicionar vídeo';
$string['allgroups'] = 'Todos os grupos permitidos';
$string['availability'] = 'Disponibilidade';
$string['availabilityerror'] = 'O fim da disponibilidade deve ser posterior ao início.';
$string['available'] = 'Disponível';
$string['availablefrom'] = 'Disponível a partir de';
$string['availableuntil'] = 'Disponível até';
$string['backtopackage'] = 'Voltar ao pacote';
$string['completed'] = 'Concluído';
$string['completioncount'] = 'Quantidade mínima de vídeos concluídos';
$string['completiondescription'] = 'Atender ao requisito configurado para o pacote de vídeos';
$string['completionmode'] = 'Regra de conclusão';
$string['completionmode_allrequired'] = 'Todos os vídeos obrigatórios concluídos';
$string['completionmode_count'] = 'Pelo menos X vídeos concluídos';
$string['completionmode_weighted'] = 'Progresso ponderado do pacote atingir X%';
$string['completionpercent'] = 'Progresso ponderado mínimo';
$string['completiontracked'] = 'Exigir regra de acompanhamento do pacote';
$string['completiontracked_desc'] = 'Concluir a atividade quando a regra configurada para o pacote for atendida.';
$string['confirmdelete'] = 'Excluir o vídeo "{$a}"?';
$string['countgreaterthanitems'] = 'A quantidade exigida pode ser maior que o número atual de vídeos; a conclusão permanecerá pendente até existirem vídeos suficientes.';
$string['currentposition'] = 'Posição atual';
$string['deletevideo'] = 'Excluir vídeo';
$string['details'] = 'Detalhes';
$string['duration'] = 'Duração';
$string['editvideo'] = 'Editar vídeo';
$string['eventcoursemoduleviewed'] = 'Super Video Tracker visualizado';
$string['eventmediaviewed'] = 'Vídeo acompanhado visualizado';
$string['eventreportviewed'] = 'Relatório do Super Video Tracker visualizado';
$string['exportcsv'] = 'Exportar CSV';
$string['inprogress'] = 'Em andamento';
$string['invaliditem'] = 'O vídeo solicitado não pertence a esta atividade.';
$string['lastview'] = 'Última visualização';
$string['learner'] = 'Aluno';
$string['managevideos'] = 'Gerenciar vídeos';
$string['minpercent'] = 'Percentual mínimo assistido';
$string['modulename'] = 'Super Video Tracker';
$string['modulenameplural'] = 'Super Video Trackers';
$string['movedown'] = 'Mover para baixo';
$string['moveup'] = 'Mover para cima';
$string['name'] = 'Nome';
$string['never'] = 'Nunca';
$string['notstarted'] = 'Não iniciado';
$string['novideos'] = 'Nenhum vídeo foi adicionado ainda.';
$string['openvideo'] = 'Abrir vídeo';
$string['optional'] = 'Opcional';
$string['overall'] = 'Geral';
$string['overallprogress'] = 'Progresso geral';
$string['overdue'] = 'Atrasado';
$string['packageprogresshelp'] = 'Progresso do pacote';
$string['packageprogresshelp_help'] = 'Vídeos opcionais não reduzem o progresso geral. Os vídeos obrigatórios e ativos entram nos cálculos simples e ponderado.';
$string['percent'] = 'Percentual';
$string['percentageerror'] = 'O percentual deve estar entre 1 e 100.';
$string['pluginadministration'] = 'Administração do Super Video Tracker';
$string['pluginname'] = 'Super Video Tracker';
$string['privacy:metadata'] = 'O Super Video Tracker armazena apenas configuração do pacote e das mídias. O progresso de reprodução é armazenado pelo local_video_bridge.';
$string['progressmode'] = 'Cálculo do progresso do pacote';
$string['progressmode_simple'] = 'Média simples dos vídeos obrigatórios';
$string['progressmode_weighted'] = 'Média ponderada dos vídeos obrigatórios';
$string['report'] = 'Relatório';
$string['reportmatrix'] = 'Matriz aluno × vídeo';
$string['required'] = 'Obrigatório';
$string['resume'] = 'Retomar de {$a}';
$string['shortdescription'] = 'Descrição curta';
$string['source'] = 'Fonte do vídeo';
$string['sourcehelp'] = 'Fontes de vídeo com tracking';
$string['sourcehelp_help'] = 'Somente providers do Video Bridge que garantem tracking podem ser selecionados.';
$string['status'] = 'Status';
$string['studentdetails'] = 'Detalhes do aluno';
$string['summary_completed'] = 'Concluídos';
$string['summary_inprogress'] = 'Em andamento';
$string['summary_notstarted'] = 'Não iniciados';
$string['summary_overdue'] = 'Atrasados';
$string['supervideotracker:addinstance'] = 'Adicionar uma atividade Super Video Tracker';
$string['supervideotracker:betracked'] = 'Ser incluído como aluno acompanhado';
$string['supervideotracker:manageitems'] = 'Gerenciar vídeos acompanhados';
$string['supervideotracker:view'] = 'Visualizar Super Video Tracker';
$string['supervideotracker:viewreports'] = 'Visualizar relatórios consolidados de vídeo';
$string['title'] = 'Título';
$string['trackingrequired'] = 'A fonte selecionada não oferece tracking confiável.';
$string['unavailable'] = 'Indisponível';
$string['videos'] = 'Vídeos';
$string['viewingmap'] = 'Mapa de visualização';
$string['weight'] = 'Peso';
$string['weighterror'] = 'O peso deve ser maior que zero.';
