<?php

namespace Library\Util;
use Library\Util\FPDF;

class PDF extends FPDF {

    function Header() {
        global $title;
        // Arial bold 15
        $this->SetFont('Times', 'B', 10);
        // Calculamos ancho y posición del título.
        $w = $this->GetStringWidth($title) + 6;
        $this->SetX((210 - $w) / 2);
        // Colores de los bordes, fondo y texto
        $this->SetFillColor(255, 255, 255);
        $this->SetTextColor(150, 150, 150);
        // Título
        $this->Cell($w, 9, $title, 0, 1, 'C', true);
        // Salto de línea
        $this->Ln(10);
    }

    function addTitle($label) {
        // Arial 12
        $this->SetFont('Times', 'I', 18);
        // Color de fondo
        $this->SetFillColor(255, 255, 255);
        // Título
        $this->Cell(0, 6, "$label", 0, 1, 'L', true);
        // Salto de línea

        $this->Ln(4);
        $this->Ln(4);
    }

    function ChapterBody($file) {
        // Times 12
        $this->SetFont('Times', '', 12);
        // Imprimimos el texto justificado
        $this->MultiCell(0, 5, $file);
        // Salto de línea
        $this->Ln();
    }

    function addParagraph($text) {
        $this->ChapterBody($text);
    }

}
