
<section class="section-full section-content">
  <div class="section-inner">
    <div class="content-box">

      <div class="deskripsi-singkat">
        <h2>Deskripsi singkat prototipe/produk (Jika Ada)</h2>
        <p>*Deskripsi tentang keunggulan produk untuk kebutuhan pemasaran</p>
      </div>

      @if ($errors->any())
        <div style="margin:10px 0; padding:10px; border:1px solid #fca5a5; background:#fee2e2; border-radius:10px;">
          <ul style="margin:0; padding-left:18px;">
            @foreach ($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif


    <form action="{{ route('patenverif.submit.final', $verif->id) }}" method="POST">
        @csrf
        <textarea name="deskripsi" class="input-deskripsi">{{ old('deskripsi', $verif->deskripsi_singkat_prototipe) }}</textarea>
        <div class="btn-center">
          <button
            type="button"
            class="btn-prev-desk"
            data-fallback="{{ route('uploadgambarprototipe') }}"
            onclick="(history.length > 1) ? history.back() : (window.location.href=this.dataset.fallback)"
          >
            &laquo; Sebelumnya
          </button>
            <button type="submit" class="btn-selanjutnya-submit">Submit</button>
        </div>
    </form>



    </div>
  </div>
</section>