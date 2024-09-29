<div class="py-1 my-0 mb-3">
  <p class="fs-sm text-uppercase text-primary fw-bold mb-1">
    {{ trans('Matakuliah Pilihan') }}
  </p>
</div>
<div class="table-responsive">
  <table id="major-elective-subjects-table" class="table table-bordered table-vcenter" style="width: 100%">
    <thead>
      <tr>
        <th>No</th>
        <th class="text-center">Kode Matakuliah</th>
        <th class="text-center">Nama Matakuliah</th>
        <th class="text-center">SKS</th>
      </tr>
    </thead>
  </table>
</div>

<script>
  const urlElectiveTable = "{{ route('api.major.show', ['major' => $major]) }}"

</script>

@vite('resources/js/academics/majors/elective.js')
