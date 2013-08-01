<?php

require_once dirname(__FILE__) . '/inc.php';
require_once($CFG->libdir . '/fpdf/fpdf.php');
require_once("../../mod/quiz/locallib.php");

define("EINZUG_LINKS", 20);

$courseid = required_param('courseid', PARAM_INT);
$quizid = required_param('id', PARAM_INT);
$correct = optional_param('correct', 0, PARAM_INT);

require_login($courseid);

$context = get_context_instance(CONTEXT_SYSTEM);
$quiz = get_record('quiz', 'id', $quizid);
	
if(!block_rk_fragesystem_check_owner($quizid))
	error('No permission to do this');

	

$sql = "SELECT q.*" .
        "  FROM {$CFG->prefix}question q" .
        " WHERE q.id IN ($quiz->questions)";
		
$questionids = explode(",",$quiz->questions);
$questions = array();

foreach($questionids as $id) {
	$qcandidate = get_record('question','id',$id);
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
if (!$course = get_record("course", "id", $courseid)) {
    print_error("invalidinstance", "rk_fragesystem");
}

//block_rk_fragesystem_print_header("mytests");
?>
<?php

class PDF extends FPDF {

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
        $this->SetY(280);
        $this->SetX(EINZUG_LINKS);
        $this->Line(EINZUG_LINKS, 277, 190, 277);
        // Arial italic 8
        $this->SetFont('Arial', '', 9);
        // Page number
        $this->Cell(0, 0, 'Erreichte Punkte dieser Seite:     ____			           Gesamtpunkte dieser Seite:	'.$this->pagequestions, 0, 1);
        $this->SetX(EINZUG_LINKS);
        $this->SetFont('Arial', '', 9);
        $this->Cell(0, 8, 'Erreichte Punkte bisher:              ____', 0, 1);

        $this->SetFont('Arial', 'B', 9);
        $this->Cell(0, 0, 'Seite ' . $this->PageNo() . ' von {nb}', 0, 0, 'C');
    }

    function PrintQuestion($question, $answers, $i, $correct) {

        // Arial 12
        $this->SetFont('Arial', 'B', 12);
        $this->Ln();
        // Title
		if($this->GetY() > 240)
			$this->AddPage();
        $this->SetX(EINZUG_LINKS);
		
		//Zeilenumbruch hinzufügen
        $this->MultiCell(0, 6, $i . '    ' . utf8_decode($question->questiontext), 0, 1, 'L');

        // Antworten
        $a = 0;
        $this->SetFont('Arial', '', 10);
        $this->Ln(2);
        foreach ($answers as $answer) {

            if ($question->qtype == 'multichoice' || $question->qtype == 'truefalse') {
                $this->SetX(EINZUG_LINKS);
                if ($correct == 1 && $answer->fraction > 0) {
					$this->SetX(EINZUG_LINKS);
					$this->Cell(0, 4, '[ x ]',0,'L');
					$this->SetX(EINZUG_LINKS+8);
					$this->MultiCell(0, 4,utf8_decode($answer->answer),0,'L');
					}
                else {
					$this->SetX(EINZUG_LINKS);
					$this->Cell(0, 4, '[   ]',0,'L');
					$this->SetX(EINZUG_LINKS+8);
					$this->MultiCell(0, 4,utf8_decode($answer->answer),0,'L');
					
					//$this->MultiCell(0, 4, '[   ]   ' . utf8_decode($answer->answer),0,'L');
				}
                $a++;
                if ($a < count($answers))
                    $this->Ln(2);
            }
            if($question->qtype == 'shortanswer') {
                $this->setX(EINZUG_LINKS);
                if ($correct == 1 && $answer->fraction == 1)
                    $this->MultiCell(0, 4, '	'.utf8_decode($answer->answer));
                else
                    $this->Ln(4);
            }
        }

        $this->Ln(4);
		$this->pagequestions++;
    }

}

// Instanciation of inherited class
$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();

// Testname
$pdf->SetFont('Arial', '', 18);
// Move to the right


$pdf->Cell(0, 30, utf8_decode($quiz->name), 0, 0, 'C');
$pdf->Ln('15');
$pdf->SetFont('Arial', '', 12);
$pdf->SetX(EINZUG_LINKS);
$pdf->Cell(0, 0, 'Vorname: ______________________  Zuname: ____________________', 0, 0, 'L');
$pdf->SetX(EINZUG_LINKS);
$pdf->Cell(0, 15, 'Punkteanzahl:  __________________  von ' . count($questions), 0, 0, 'L');
$pdf->Ln(8);

$i = 1;
foreach ($questions as $question) {
    $answers = get_records('question_answers', 'question', $question->id);
    if ($answers) {
        $pdf->PrintQuestion($question, $answers, $i, $correct);
        $i++;
    }
}

$pdf->Output();
?>