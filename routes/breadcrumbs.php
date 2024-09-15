<?php // routes/breadcrumbs.php

use App\Models\Grade;
use App\Models\Student;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// Home
Breadcrumbs::for('home', function (BreadcrumbTrail $trail) {
  $trail->push(trans('page.overview.title'), route('home'));
});
// Home

// Roles Breadcrumbs
Breadcrumbs::for('roles.index', function (BreadcrumbTrail $trail) {
  $trail->parent('home');
  $trail->push(trans('page.roles.index'), route('roles.index'));
});

Breadcrumbs::for('roles.create', function (BreadcrumbTrail $trail) {
  $trail->parent('roles.index');
  $trail->push(trans('page.roles.create'), route('roles.create'));
});

Breadcrumbs::for('roles.edit', function (BreadcrumbTrail $trail, $role) {
  $trail->parent('roles.index');
  $trail->push(trans('page.roles.edit'), route('roles.edit', $role->uuid));
});

Breadcrumbs::for('roles.show', function (BreadcrumbTrail $trail, $role) {
  $trail->parent('roles.index');
  $trail->push(trans('page.roles.show'), route('roles.show', $role->uuid));
});
// Roles Breadcrumbs

// Users Breadcrumbs
Breadcrumbs::for('users.index', function (BreadcrumbTrail $trail) {
  $trail->parent('home');
  $trail->push(trans('page.users.index'), route('users.index'));
});

Breadcrumbs::for('users.create', function (BreadcrumbTrail $trail) {
  $trail->parent('users.index');
  $trail->push(trans('page.users.create'), route('users.create'));
});

Breadcrumbs::for('users.edit', function (BreadcrumbTrail $trail, $user) {
  $trail->parent('users.index');
  $trail->push(trans('page.users.edit'), route('users.edit', $user->uuid));
});

Breadcrumbs::for('users.show', function (BreadcrumbTrail $trail, $user) {
  $trail->parent('users.index');
  $trail->push(trans('page.users.show'), route('users.show', $user->uuid));
});
// Users Breadcrumbs

// Majors Breadcrumbs
Breadcrumbs::for('majors.index', function (BreadcrumbTrail $trail) {
  $trail->parent('home');
  $trail->push(trans('page.majors.index'), route('majors.index'));
});

Breadcrumbs::for('majors.create', function (BreadcrumbTrail $trail) {
  $trail->parent('majors.index');
  $trail->push(trans('page.majors.create'), route('majors.create'));
});

Breadcrumbs::for('majors.edit', function (BreadcrumbTrail $trail, $major) {
  $trail->parent('majors.index');
  $trail->push(trans('page.majors.edit'), route('majors.edit', $major->uuid));
});

Breadcrumbs::for('majors.show', function (BreadcrumbTrail $trail, $major) {
  $trail->parent('majors.index');
  $trail->push(trans('page.majors.show'), route('majors.show', $major->uuid));
});
// Majors Breadcrumbs

// Subjects Breadcrumbs
Breadcrumbs::for('subjects.index', function (BreadcrumbTrail $trail) {
  $trail->parent('home');
  $trail->push(trans('page.subjects.index'), route('subjects.index'));
});

Breadcrumbs::for('subjects.create', function (BreadcrumbTrail $trail) {
  $trail->parent('subjects.index');
  $trail->push(trans('page.subjects.create'), route('subjects.create'));
});

Breadcrumbs::for('subjects.edit', function (BreadcrumbTrail $trail, $subject) {
  $trail->parent('subjects.index');
  $trail->push(trans('page.subjects.edit'), route('subjects.edit', $subject->uuid));
});

Breadcrumbs::for('subjects.show', function (BreadcrumbTrail $trail, $subject) {
  $trail->parent('subjects.index');
  $trail->push(trans('page.subjects.show'), route('subjects.show', $subject->uuid));
});
// Subjects Breadcrumbs

// Subjects Breadcrumbs
Breadcrumbs::for('students.index', function (BreadcrumbTrail $trail) {
  $trail->parent('home');
  $trail->push(trans('page.students.index'), route('students.index'));
});

Breadcrumbs::for('students.create', function (BreadcrumbTrail $trail) {
  $trail->parent('students.index');
  $trail->push(trans('page.students.create'), route('students.create'));
});

Breadcrumbs::for('students.edit', function (BreadcrumbTrail $trail, $student) {
  $trail->parent('students.index');
  $trail->push(trans('page.students.edit'), route('students.edit', $student->uuid));
});

Breadcrumbs::for('students.show', function (BreadcrumbTrail $trail, $student) {
  $trail->parent('students.index');
  $trail->push(trans('page.students.show'), route('students.show', $student->uuid));
});
// Subjects Breadcrumbs

// Recommendations Breadcrumbs
Breadcrumbs::for('recommendations.index', function (BreadcrumbTrail $trail) {
  $trail->parent('home');
  $trail->push(trans('Daftar Mahasiswa'), route('recommendations.index'));
});

Breadcrumbs::for('recommendations.create', function (BreadcrumbTrail $trail, Student $student) {
  $trail->parent('recommendations.show', $student);
  $trail->push(trans('page.recommendations.create'), route('recommendations.create', $student));
});

Breadcrumbs::for('recommendations.edit', function (BreadcrumbTrail $trail, $recommendation) {
  $trail->parent('recommendations.index');
  $trail->push(trans('page.recommendations.edit'), route('recommendations.edit', $recommendation->uuid));
});

Breadcrumbs::for('recommendations.show', function (BreadcrumbTrail $trail, Student $student) {
  $trail->parent('recommendations.index');
  $trail->push(trans('page.recommendations.show'), route('recommendations.show', $student));
});
// Recommendations Breadcrumbs

// grades Breadcrumbs
Breadcrumbs::for('grades.index', function (BreadcrumbTrail $trail) {
  $trail->parent('home');
  $trail->push(trans('Daftar Mahasiswa'), route('grades.index'));
});

Breadcrumbs::for('grades.create', function (BreadcrumbTrail $trail, Student $student) {
  $trail->parent('grades.index');
  $trail->push(trans('page.grades.create'), route('grades.create', $student));
});

Breadcrumbs::for('grades.show', function (BreadcrumbTrail $trail, Student $student) {
  $trail->parent('grades.index');
  $trail->push(trans('page.grades.show'), route('grades.show', $student));
});

Breadcrumbs::for('grades.edit', function (BreadcrumbTrail $trail, $grade) {
  $trail->parent('grades.show', $grade->student);
  $trail->push(trans('page.grades.edit'), route('grades.edit', [
    'grade' => $grade->uuid,
    'student' => $grade->student
  ]));
});
// grades Breadcrumbs

Breadcrumbs::for('activities.index', function (BreadcrumbTrail $trail) {
  $trail->parent('home');
  $trail->push(trans('page.activities.index'), route('activities.index'));
});

Breadcrumbs::for('activities.show', function (BreadcrumbTrail $trail, $activity) {
  $trail->parent('activities.index');
  $trail->push(trans('page.activities.show'), route('activities.show', $activity));
});
