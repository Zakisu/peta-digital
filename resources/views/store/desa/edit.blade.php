@extends('layout.master')
@section('title')
  Edit Desa
@endsection
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body"> 
          <h4 class="card-title">Edit Desa</h4>
          <form 
            class="mt-4" 
            @submit.prevent="storeData()" 
            @keydown="form.onKeydown($event)" 
            id="form" 
            enctype="multipart/form-data"
          >
            <div class="form-body">
              <div class="row">
                <div class="col-md-12">
                  <label class="col-lg-2" for="title"> Nama Desa </label>
                  <div class="form-group col-md-12">
                    <input v-model="form.title" id="title" type="text" min=0 placeholder="Nama Desa"
                        class="form-control" :class="{ 'is-invalid': this.error.title }">
                    <div class="invalid-feedback">
                      Nama Desa is required
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                  <label class="col-lg-2" for="description"> Deskripsi </label>
                  <div class="form-group col-md-12">
                    <textarea v-model="form.description" id="description" type="text" min=0 placeholder="Masukkan deskripsi"
                        class="form-control" :class="{ 'is-invalid': this.error.description  }" rows="5"></textarea>
                    <div class="invalid-feedback">
                      Deskripsi is required
                    </div>
                  </div>
                </div>
                <div class="col-md-12" v-show="false">
                  <label class="col-lg-2" for="latitude"> latitude </label>
                  <div class="form-group col-md-12">
                    <input id="latitude" type="text" min=0 placeholder="Masukkan latitude"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('latitude') }">
                    <has-error :form="form" field="latitude"></has-error>
                  </div>
                </div>
                <div class="col-md-12" v-show="false">
                  <label class="col-lg-2" for="longitude"> longitude </label>
                  <div class="form-group col-md-12">
                    <input id="longitude" type="text" min=0 placeholder="Masukkan longitude"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('longitude') }">
                    <has-error :form="form" field="longitude"></has-error>
                  </div>
                </div>
                <div class="col-md-12" >
                  <label class="col-lg-2" for="image"> Upload Gambar </label>
                  <div class="form-group col-md-12">
                    <input id="images" class="form-control" style="height: 45px !important;" type="file" @change="upload($event)" multiple :class="{ 'is-invalid': this.error.images }">
                    <div class="invalid-feedback">
                      Images is required
                    </div>
                  </div>
                </div>
                <div class="col-md-12 mt-4 mb-4" style="z-index: 1000" >
                  <label class="col-lg-2" for="description"> Pilih Lokasi Desa </label>
                  <div class="form-group col-md-12">
                    <input v-show="false" class="form-control" :class="{ 'is-invalid': this.error.coordinate }">
                    <div id="map" style="width:100%;height: 40vh"></div>
                    <div class="invalid-feedback">
                      Location is required
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-actions">
              <div class="text-left">
                  <button type="submit" class="btn btn-info">Tambah</button>
                  <button type="reset" id="reset" class="btn btn-dark" @click="resetData()">Reset</button>
              </div>
            </div>
          </form>
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
        images : [],
        coordinate :''
      }),
      editorData: null,
      error : {
        title : false,
        description: false,
        images: false,
        coordinate : false,
      }
    },
    mounted() {
      this.refreshData();
      ClassicEditor
      .create( document.querySelector( '#description' ) )
      .then( editor => {
        this.editorData = editor;
      } )
      .catch( error => {
        console.error( error );
      });
    },
    methods: {
      upload(event) {
        for (let file of event.target.files) {
          try {
            let reader = new FileReader();
            reader.readAsDataURL(file);
            this.form.images.push(file);
          } catch {}
        }
      },
      storeData(){
        let latitude = $('#latitude').val()
        let longitude = $('#longitude').val()
        this.form.coordinate = latitude.concat("|", longitude)
        
        if(this.form.title === ""){
          this.error.title = true
        }else if(this.editorData.getData() === ""){
          this.error.description = true
        }else if(this.form.images.length === 0){
          this.error.images = true
        }else if (latitude === "" || longitude === ""){
          this.error.coordinate = true
        }else{
          let params = new FormData()
          params.append("title", this.form.title)
          params.append("description", this.editorData.getData())
          params.append("coordinate", this.form.coordinate)
          for(let i=0; i < this.form.images.length; i++){
            params.append("images[]", this.form.images[i])
          }
          url = "{{ route('village.update', ':id') }}".replace(':id', 23)
          axios.post(url,params)
          .then(response => {
            Swal.fire(
                'Berhasil',
                'Data desa berhasil ditambahkan',
                'success'
            )
            window.location.href('/desa');
          }).catch(e => {
              e.response.status != 422 ? console.log(e) : '';
          })
        }
      },
      resetData(){
        this.form.description = ""
        this.form.title = ""
        this.form.images = []
        this.editorData.setData("",function() { this.updateElement(); })
      },
      refreshData() {
        console.log('tes')
        url = "{{ route('village.detail', ':id') }}".replace(':id', 23)
        axios.get(url)
        .then(response => {
          this.form.title = response.data.title
          this.editorData.setData(response.data.description) 
          let splitCoordinate = response.data.coordinate.split("|")
          $('#latitude').val(splitCoordinate[0]);
          $('#longitude').val(splitCoordinate[1]);
          console.log('res',response)
        })
        .catch(e => {
          e.response.status != 422 ? console.log(e) : '';
        })
      }
    },
  })
</script>

<script>
  setTimeout(function () {
    window.dispatchEvent(new Event('resize'));
  }, 100);
  
  var mapCenter = [{{ request('latitude', config('leaflet.map_center_latitude')) }}, {{ request('longitude', config('leaflet.map_center_longitude')) }}];
  var map = L.map('map').setView(mapCenter, {{ config('leaflet.zoom_level') }});

  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
  }).addTo(map);

  var marker = L.marker(mapCenter).addTo(map);
  function updateMarker(lat, lng) {
      marker
      .setLatLng([lat, lng])
      .bindPopup("Your location :  " + marker.getLatLng().toString())
      .openPopup();
      return false;
  };

  map.on('click', function(e) {
    let latitude = e.latlng.lat.toString().substring(0, 15);
    let longitude = e.latlng.lng.toString().substring(0, 15);
    $('#latitude').val(latitude);
    $('#longitude').val(longitude);
    updateMarker(latitude, longitude);
  });

  var updateMarkerByInputs = function() {
    return updateMarker( $('#latitude').val() , $('#longitude').val());
  }
  $('#latitude').on('input', updateMarkerByInputs);
  $('#longitude').on('input', updateMarkerByInputs);

  $('#reset').click(function(){
    map.removeLayer(marker)
  });
</script>
@endpush
