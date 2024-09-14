<?php

return [
  'users' => [
    'create' => 'Menambahkan Pengguna baru dengan nama: :user',
    'edit' => 'Mengubah data Pengguna dengan nama: :user',
    'status' => 'Mengubah status Pengguna dengan nama: :user',
    'destroy' => 'Menghapus data Pengguna dengan nama: :user',
  ],

  'roles' => [
    'create' => 'Menambahkan Role & Permission dengan nama: :role',
    'edit' => 'Mengubah data Role & Permission dengan nama: :role',
    'status' => 'Mengubah status Role & Permission dengan nama: :role',
    'destroy' => 'Menghapus data Role & Permission dengan nama: :role',
  ],

  'majors' => [
    'create' => 'Menambahkan Jurusan dengan nama: :major',
    'edit' => 'Mengubah data Jurusan dengan nama: :major',
    'status' => 'Mengubah status Jurusan dengan nama: :major',
    'destroy' => 'Menghapus data Jurusan dengan nama: :major',
    'import' => 'Mengimport data Jurusan dari Excel',
    'subjects' => [
      'create' => 'Menambahkan Matakuliah: :subject ke Prodi: :major',
      'destroy' => 'Menghapus Matakuliah: :subject dari Prodi: :major',
    ],
  ],

  'subjects' => [
    'create' => 'Menambahkan Matakuliah dengan nama: :subject',
    'edit' => 'Mengubah data Matakuliah dengan nama: :subject',
    'status' => 'Mengubah status Matakuliah dengan nama: :subject',
    'destroy' => 'Menghapus data Matakuliah dengan nama: :subject',
    'import' => 'Mengimport data Matakuliah dari Excel',
  ],

  'students' => [
    'create' => 'Menambahkan Mahasiswa dengan nama: :student',
    'edit' => 'Mengubah data Mahasiswa dengan nama: :student',
    'status' => 'Mengubah status Mahasiswa dengan nama: :student',
    'destroy' => 'Menghapus data Mahasiswa dengan nama: :student',
    'restore' => 'Memulihkan data Mahasiswa dengan nama: :student',
    'delete' => 'Menghapus data Mahasiswa secara permanen dengan nama: :student',
    'import' => 'Mengimport data Mahasiswa dari Excel',
  ],

  'recommendations' => [
    'create' => 'Menambahkan Rekomendasi dengan nama Matakuliah: :recommendation',
    'edit' => 'Mengubah data Rekomendasi dengan nama Matakuliah: :recommendation',
    'status' => 'Mengubah status Rekomendasi dengan nama Matakuliah: :recommendation',
    'destroy' => 'Menghapus data Rekomendasi dengan nama Matakuliah: :recommendation',
    'import' => 'Mengimport data Rekomendasi dari Excel',
  ],

  'grades' => [
    'create' => 'Menambahkan Nilai dengan nama Matakuliah: :grade',
    'edit' => 'Mengubah data Nilai dengan nama Matakuliah: :grade',
    'status' => 'Mengubah status Nilai dengan nama Matakuliah: :grade',
    'destroy' => 'Menghapus data Nilai dengan nama Matakuliah: :grade',
    'import' => 'Mengimport data Nilai dari Excel',
  ],
];
