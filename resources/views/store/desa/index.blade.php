@extends('layout.master')
@section('title')
  Desa
@endsection
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title">Daftar Desa
            <button type="button" class="btn btn-primary btn-rounded float-right mb-3" >
            <a href="{{url('/desa/tambah')}}" style="color: white;"> <i class="fas fa-plus-circle"></i> Tambah Desa</a></button>
          </h4>
          <div class="table-responsive">
            <table id="table" class="table table-striped table-bordered no-wrap">
              <thead>
                <tr>
                  <th>No</th>
                  <th>Nama</th>
                  <th>Langitude</th>
                  <th>Longitude</th>
                  <th>Deskripsi</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item, index in mainData" :key="index">
                  <td>@{{ index+1 }}</td>
                  <td>@{{ item.title }}</td>
                  <td>@{{ item.latitude }}</td>
                  <td>@{{ item.longitude }}</td>
                  <td v-html="item.description"></td>
                  <td>
                    <a href="javascript:void(0);" @click="editData(item.id)" class="text-success"
                      data-toggle="tooltip" data-placement="top" data-original-title="Edit"><i
                      class="far fa-edit"></i></a>
                    <a href="javascript:void(0);" @click="deleteData(item.id)" class="text-danger"
                      data-toggle="tooltip" data-placement="top" data-original-title="Hapus"><i
                      class="far fa-trash-alt"></i></a>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection


@push('script')
<script>
  var app = new Vue({
    el: '#app',
    data: {
      mainData: [],
      form: new Form({
        id: '',
        title: '',
        description: '',
        lat: '',
        lng: '',
        images : [],
      }),
      editMode: false,
    },
    mounted() {
      this.refreshData()
    },
    methods: {
      upload(event) {
        for (let file of event.target.images) {
          try {
            let reader = new FileReader();
            reader.readAsDataURL(file);
            this.form.images.push(file);
          } catch {}
        }
      },
      editData(id){
        url = "{{ route('village.edit', ':id') }}".replace(':id', id)
        window.location = url
      },
      deleteData(id){
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Anda tidak dapat mengembalikan ini",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.value) {
              url = "{{ route('village.destroy', ':id') }}".replace(':id', id)
              this.form.delete(url)
              .then(response => {
                console.log('res',response)
                Swal.fire(
                  'Terhapus',
                  'Daftar Desa telah dihapus',
                  'success'
                )
                this.refreshData()
              })
              .catch(e => {
                  e.response.status != 422 ? console.log(e) : '';
              })
            }
        })
      },
      refreshData() {
        axios.get("{{ route('village.list') }}")
        .then(response => {
          if (response.data.length !== 0){
            for(let data of response.data){
              let splitCoordinate = data.coordinate.split("|")
              data.latitude = splitCoordinate[0]
              data.longitude = splitCoordinate[1]
              this.mainData.push(data)      
            }
          }else{
            this.mainData = [];
          }
         
          $('#table').DataTable().destroy()
          this.$nextTick(function () {
              $('#table').DataTable();
          })
        })
        .catch(e => {
          e.response.status != 422 ? console.log(e) : '';
        })
      }
    },
  })
</script>
@endpush
