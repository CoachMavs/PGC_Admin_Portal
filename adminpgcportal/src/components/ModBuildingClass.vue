<template>
  <h1 class="card-header">Operations and Maintenance Percentage</h1>

  <div class="card">

    <div class="card-body">
      <v-text-field
        hide-details
        type="number"
        label="Cost range to:*"
        variant="outlined"
        step="1000000" 
        required
      ></v-text-field>

      <v-text-field
        hide-details
        type="number"
        label="Cost range to:*"
        variant="outlined"
        step="1000000" 
        required
      ></v-text-field>

      <table class="table">
        <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">Building Classification</th>
            <th scope="col">Building Cost</th>
            <th scope="col">Ratio of BLDG Cost</th>
            <th scope="col">Action</th>
          </tr>
        </thead>
      
      </table>

      <v-btn color="#1770d6"  rounded="xl" >Add Building</v-btn>    
    </div>
  </div>

</template>
