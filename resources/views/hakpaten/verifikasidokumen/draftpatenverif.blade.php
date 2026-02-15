<section class="section-full section-content section-upload-full">
  <div class="section-inner section-upload-full__inner">
    <div class="content-isi">
      <div class="draft-paten">
        <h2>Draft Paten <span class="req">*</span></h2>
      </div>

      <div class="hero-buttons-start">
        <div class="button-upload">
          <form
            id="draftForm"
            method="POST"
            action="{{ route('patenverif.upload.draft', ['verif' => $verif->id]) }}"
            enctype="multipart/form-data"
          >
            @csrf

            <input id="draftFile" type="file" name="file" hidden required accept=".doc,.docx,.pdf">

            <button id="uploadButton" type="button" class="btn-upload">
              Upload
            </button>

            <span id="fileName" class="file-name">
              @if($verif->draft_paten)
                {{ basename($verif->draft_paten) }}
              @else
                Belum pilih file
              @endif
            </span>

            <button id="submitUpload" type="submit" style="display:none;">Kirim</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>
