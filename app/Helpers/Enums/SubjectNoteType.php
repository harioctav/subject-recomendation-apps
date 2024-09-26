<?php

namespace App\Helpers\Enums;

use App\Traits\EnumsToArray;

enum SubjectNoteType: string
{
  use EnumsToArray;

  case T = 'TAP';
  case P = 'P';
  case PR = 'PR';
  case E = 'E';
  case BW = 'BW';
  case PS = 'PILIH SALAH SATU';
  case BPR = 'BPR';
  case PRO = 'PRO';
  case TW = 'TW';
  case BPRO = 'BPRO';
  case L = 'L';
}

// 1. T (Tutorial tatap muka, mata kuliah sulit)
// 2. P (Praktik)
// 3. Pr (Praktikum)
// 4. E (ujian essai)
// 5. Bw  ( Menuntut bimbingan wajib)
// 6. PS (pilih salah satu)
// 7. Bpr (Berpraktik / berpraktikum)
// 8. Pro (Bimbingan praktik online)
// 9. Tw (tutorial webinar)
// 10. Bpro (berpraktik online)
// 11. L (menuntut laporan)