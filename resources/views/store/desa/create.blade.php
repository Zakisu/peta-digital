@extends('layout.master')
@section('title')
  Tambah Desa
@endsection
@section('content')
<div class="container-fluid">
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body"> 
          <h4 class="card-title">Tambah Desa</h4>
          <form 
            class="mt-4" 
            @submit.prevent="editMode ? updateData() : storeData()" 
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
                        class="form-control" :class="{ 'is-invalid': form.errors.has('title') }">
                    <has-error :form="form" field="title"></has-error>
                  </div>
                </div>
                <div class="col-md-12">
                  <label class="col-lg-2" for="description"> Deskripsi </label>
                  <div class="form-group col-md-12">
                    <textarea v-model="form.description" id="description" type="text" min=0 placeholder="Masukkan deskripsi"
                        class="form-control" :class="{ 'is-invalid': form.errors.has('description') }"></textarea>
                    <has-error :form="form" field="description"></has-error>
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
                  <input type="file" @change="upload($event)" class="form-control" multiple>
                </div>
                <div class="col-md-12 mt-4 mb-4" style="z-index: 1000" >
                  <label class="col-lg-2" for="description"> Pilih Lokasi Desa </label>
                  <div class="form-group col-md-12">
                  <div id="map" style="width:100%;height: 40vh"></div>
                  </div>
                </div>
              </div>
            </div>
            <div class="form-actions">
              <div class="text-left">
                  <button type="submit" class="btn btn-info">Submit</button>
                  <button type="reset" class="btn btn-dark">Reset</button>
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
      createModal(){
        this.editMode = false;
        this.form.reset();
        this.form.clear();
      },
      storeData(){

        let latitude = $('#latitude').val()
        let longitude = $('#longitude').val()
        let coordinate = latitude.concat("|", longitude)

        console.log('coordinate',coordinate)

        // let params = new FormData()
        // params.append("title", this.form.title)
        // params.append("description", this.form.description)
        // // params.append("coordinate", )
        // for(let i=0; i < this.form.images.length; i++){
        //   params.append("images[]", this.form.images[i])
        // }
        // axios.post("{{ route('village.store') }}",params)
        // .then(response => {
        //     console.log('res',response)
        // })
      },
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
</script>
@endpush
