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
                  <th>Koordinat</th>
                  <th>Deskripsi</th>
                  <th>Aksi</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="item, index in mainData" :key="index">
                  <td>@{{ index+1 }}</td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td>
                    <a href="javascript:void(0);" @click="editData(item)" class="text-success"
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
              // this.form.delete(url)
              // .then(response => {
              //   Swal.fire(
              //     'Terhapus',
              //     'Sediaan Obat telah dihapus',
              //     'success'
              //   )
              //   this.refreshData()
              // })
              // .catch(e => {
              //     e.response.status != 422 ? console.log(e) : '';
              // })
            }
        })
      },
      refreshData() {
        console.log('blog',this.mainData)
      }
    },
  })
</script>
@endpush
