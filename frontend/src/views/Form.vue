<template>
  <div id="form" class="container">
    <h2>Formulário</h2>
    <div class="before-form">
      <form action="http://localhost:8000/csv/add" method="post" enctype=multipart/form-data>
        <div class="form-field">
          <label>Selecione os arquivos do tipo CSV</label>
          <input type="file" name="csv[]" multiple />
        </div>
        <button type="submit">Enviar</button>
      </form>
    </div>
  </div>
</template>
<style>
.container {
  padding: 10px;
  margin: 10px;
}
.before-form {
  background-color: #fff;
  padding: 10px;
}
.before-form form {
  background-color: #f1f1f1;
  width: 800px;
  padding: 30px;
  margin: auto;
}
.form-field label {
  width: 100%;
}
.form-field input {
  width: 100%;
  margin: 20px auto;
}
</style>