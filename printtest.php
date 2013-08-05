<?php

require_once dirname(__FILE__) . '/inc.php';
require_once($CFG->dirroot.'/lib/tcpdf/tcpdf.php');
require_once("../../mod/quiz/locallib.php");

define("EINZUG_LINKS", 20);

$courseid = required_param('courseid', PARAM_INT);
$quizid = required_param('id', PARAM_INT);
$correct = optional_param('correct', 0, PARAM_INT);

require_login($courseid);

global $DB;

$context = get_context_instance(CONTEXT_SYSTEM);
$quiz = $DB->get_record('quiz', array('id' => $quizid));

if(!block_rk_fragesystem_check_owner($quizid))
	error('No permission to do this');

$sql = "SELECT q.*" .
		"  FROM {question q}" .
		" WHERE q.id IN ($quiz->questions)";

$questionids = explode(",",$quiz->questions);
$questions = array();
foreach($questionids as $id) {
	if($id == 0)
		continue;

	$qcandidate = $DB->get_record('question',array('id'=>$id));
	if ($qcandidate->qtype == 'multichoice' || $qcandidate->qtype == 'truefalse' || $qcandidate->qtype == 'shortanswer')
		$questions[] = $qcandidate;
}
if (!$questions) {
	error('No questions found');
}
// Load the question type specific information
if (!get_question_options($questions)) {
	error('Could not load question options');
}
if (!$course = $DB->get_record("course", array("id"=>$courseid))) {
	print_error("invalidinstance", "rk_fragesystem");
}

//block_rk_fragesystem_print_header("mytests");
?>
<?php

class PDF extends TCPDF {

	var $pagequestions = 0;
	// Page header
	function Header() {
		// Logo
		$this->pagequestions = 0;
		$this->Image('pix/logo2.jpg', 23, 0, 160);
		$this->Ln(15);
	}

	// Page footer
	function Footer() {
		// Position at 1.5 cm from bottom
		$this->SetY(275);
		$this->SetX(EINZUG_LINKS);
		$this->Line(EINZUG_LINKS, 272, 190, 272);
		// helvetica italic 8
		$this->SetFont('helvetica', '', 9);
		// Page number
		$this->Cell(0, 0, 'Erreichte Punkte dieser Seite:     ____			           Gesamtpunkte dieser Seite:	'.$this->pagequestions, 0, 1);
		$this->SetX(EINZUG_LINKS);
		$this->SetFont('helvetica', '', 9);
		$this->Cell(0, 8, 'Erreichte Punkte bisher:              ____', 0, 1);

		$this->SetFont('helvetica', 'B', 9);
		$this->Cell(0, 0, 'Seite ' . $this->PageNo() . ' von ' . $this->getAliasNbPages(), 0, 0, 'C');
	}

	function PrintQuestion($question, $answers, $i, $correct) {

		// helvetica 12
		$this->SetFont('helvetica', 'B', 11);
		$this->Ln();
		// Title
		if($this->GetY() > 230) {
			$this->AddPage();
			$this->SetY(20);
		}
		$this->SetX(EINZUG_LINKS);

		//Zeilenumbruch hinzufügen
		$this->MultiCell(0, 6, $i . '    ' . strip_tags(html_entity_decode($question->questiontext, ENT_QUOTES, "UTF-8")), 0, 'L');

		// Antworten
		$a = 0;
		$this->SetFont('helvetica', '', 10);
		$this->Ln(2);
		foreach ($answers as $answer) {

			if ($question->qtype == 'multichoice' || $question->qtype == 'truefalse') {
				$this->SetX(EINZUG_LINKS);
				if ($correct == 1 && $answer->fraction > 0) {
					$this->SetX(EINZUG_LINKS);
					$this->Cell(0, 4, '[ x ]',0,'L');
					$this->SetX(EINZUG_LINKS+8);
					$this->MultiCell(0, 4,strip_tags(html_entity_decode($answer->answer, ENT_QUOTES, "UTF-8")),0,'L');
				}
				else {
					$this->SetX(EINZUG_LINKS);
					$this->Cell(0, 4, '[   ]',0,'L');
					$this->SetX(EINZUG_LINKS+8);
					$this->MultiCell(0, 4,strip_tags(html_entity_decode($answer->answer, ENT_QUOTES, "UTF-8")),0,'L');
						
					//$this->MultiCell(0, 4, '[   ]   ' . utf8_decode($answer->answer),0,'L');
				}
				$a++;
				if ($a < count($answers))
					$this->Ln(2);
			}
			if($question->qtype == 'shortanswer') {
				$this->setX(EINZUG_LINKS);
				if ($correct == 1 && $answer->fraction == 1)
					$this->MultiCell(0, 4, '	'.strip_tags(html_entity_decode($answer->answer, ENT_QUOTES, "UTF-8")));
				else
					$this->Ln(4);
			}
		}

		$this->Ln(4);
		$this->pagequestions++;

	}
	function __construct() {
		parent::__construct();
		$encoding = 'ISO-8859-1';
	}

}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Testname
$pdf->SetFont('helvetica', '', 18);
// Move to the right


$pdf->Cell(0, 30, utf8_decode($quiz->name), 0, 0, 'C');
$pdf->Ln('15');
$pdf->SetFont('helvetica', '', 12);
$pdf->SetX(EINZUG_LINKS);
$pdf->Cell(0, 0, 'Vorname: ______________________  Zuname: ____________________', 0, 0, 'L');
$pdf->SetX(EINZUG_LINKS);
$pdf->Cell(0, 15, 'Punkteanzahl:  __________________  von ' . count($questions), 0, 0, 'L');
$pdf->Ln(8);

$i = 1;
foreach ($questions as $question) {
	$answers = $DB->get_records('question_answers', array('question' => $question->id));
	if ($answers) {
		$pdf->PrintQuestion($question, $answers, $i, $correct);
		$i++;
	}
}

$pdf->Output();
?>