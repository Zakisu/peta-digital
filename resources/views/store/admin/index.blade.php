@extends('layout.master')
@section('title')
Admin
@endsection
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <h4 class="card-title"> Daftar Admin
            <button type="button" class="btn btn-primary btn-rounded float-right mb-3" @click="createModal()">
            <i class="fas fa-plus-circle"></i> Tambah Admin</button>
          </h4>
          
          <div class="table-responsive">
            <table id="table" class="table table-striped table-bordered no-wrap">
              <thead>
                  <tr>
                      <th>No</th>
                      <th>Email</th>
                      <th>Aksi</th>
                  </tr>
              </thead>
              <tbody>
                <tr v-for="item, index in mainData" :key="index">
                  <td>@{{ index+1 }}</td>
                  <td>@{{ item.nama_bentuk == 'null' ? '' : item.nama_bentuk}}</td>
                  <td>
                    <a href="javascript:void(0);" @click="editModal(item)" class="text-success"
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

<!-- MODAL -->
<div id="modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg" id="modal">
    <div class="modal-content">
      <div class="modal-header ">
        <h4 class="modal-title" v-show="!editMode" id="myLargeModalLabel">Tambah Admin</h4>
        <h4 class="modal-title" v-show="editMode" id="myLargeModalLabel">Edit</h4>
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
      </div>
      <form @submit.prevent="editMode ? updateData() : storeData()" @keydown="form.onKeydown($event)" id="form">
          <div class="modal-body mx-4">
            <div class="form-row">
              <label class="col-lg-2" for="email"> Email </label>
              <div class="form-group col-md-8">
                <input v-model="form.email" id="email" type="text" min=0 placeholder="Masukkan Email"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
                <has-error :form="form" field="email"></has-error>
              </div>
            </div>
            <div class="form-row">
              <label class="col-lg-2" for="password"> Password </label>
              <div class="form-group col-md-8">
                <input v-model="form.password" id="password" type="text" min=0 placeholder="Masukkan password"
                    class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
                <has-error :form="form" field="password"></has-error>
              </div>
            </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-dismiss="modal">Batal</button>
            <button v-show="!editMode" type="submit" class="btn btn-primary">Tambah</button>
            <button v-show="editMode" type="submit" class="btn btn-success">Ubah</button>
          </div>
      </form>
    </div>
  </div>
</div>
@endsection

@push('script')
  <script>
    var app = new Vue({
      el: '#app',
      data: {
        mainData: [
          {
            data : '1',
          }
        ],
        form: new Form({
          id: '',
          email: '',
          password: '',
        }),
        editMode: false,
      },
      mounted() {
        this.refreshData()
      },
      methods: {
        createModal(){
          this.editMode = false;
          this.form.reset();
          this.form.clear();
          $('#modal').modal('show');
        },
        editModal(data){

        },
        storeData(){

        },
        updateData(){

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
