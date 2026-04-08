<?php
 function formatCost2($cost)
 {
     return number_format((float)$cost, 2, '.', ',');
 }

?>
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
      

        <!-- Styles -->
        <style>
            .custom-title {
    background-color: #1770d6;
    color: white;
}

.v-divider {
    background-color: #e0e0e0;
    height: 2px;
    margin: 5px 0;
}

.table-container {
    font-size: 0.6rem;
}

table {
    border-collapse: collapse;
    border: 1px solid black;
}

th,
td {
    border: 1px solid black;
    text-align: center;
    vertical-align: middle;
}

.inline {
    display: inline;
}

.bold {
    font-weight: bold;
}

.divider {
    border-top: 3px solid black;
    margin: 10px 0;
}

.header {
    display: flex;
    align-items: center;
    /* Align items vertically centered */
    justify-content: center;
    /* Center the items horizontally */
    text-align: center;
    /* Center text within its container */
}

.image {
    width: 100px;
    /* Adjust the width as needed */
    height: auto;
    /* Maintain the aspect ratio */
    margin-right: 20px;
    /* Space between the image and the text */
    height: 130px;
    width: 130px;
}

.text-container {
    display: flex;
    flex-direction: column;
}

.center-bold {
    text-align: center;
    font-weight: bold;
    margin: 0;
    /* Remove default margin */
}
.print-area {
  margin: 20px;
  padding: 20px;
  border: 1px solid black;
  max-height: 100vh; /* Ensure content fits within the viewport */
  overflow: hidden !important; /* Hide overflow to prevent scrollbars */
  box-sizing: border-box; /* Include padding and border in the element's total width and height */
}

/* Print Styles */
@media print {
  body {
    overflow: hidden !important; /* Hide overflow on the body */
  }
  .print-area {
    overflow: visible !important; /* Ensure content is fully visible */
    height: auto; /* Allow content to expand as needed */
    max-height: none; /* Remove any max-height restrictions */
  }
}
        </style>
    </head>
    <body >
        
        @for($i = 0; $i < count($opcost); $i++)
        <div>
                    <div class="table-container">
                        <h3>Classification: {{ $opcost[$i]->Class }}</h3>
                        <table class="table" style="border-collapse: collapse; border: 1px solid black;">
                            <thead>
                                <tr>
                                    <th rowspan="2"
                                        style="border: 1px solid black; text-align: center; vertical-align: middle;">
                                        Buildings
                                    </th>
                                    <th colspan="30"
                                        style="border: 1px solid black; text-align: center; vertical-align: middle;">
                                        YEAR</th>
                                    <!-- <th rowspan="2"
                                        style="border: 1px solid black; text-align: center; vertical-align: middle; white-space: break-word; width: 5px;">
                                        Total Maintenance Cost
                                    </th> -->
                                </tr>
                                <tr>
                                    @for($j = 1; $j <= 30; $j++)
                                    <th style="border: 1px solid black; text-align: center;">
                                        {{ $j }}</th>
                                    @endfor
                                </tr>
                            </thead>
                            <tbody>
                              
                          
                            <tr>
                                
                                    <td style="border: 1px solid black;">{{ $opcost[$i]->name }}</td>
                                    
                                        <!-- Rotate text vertically in item[i] cells -->
                                        @foreach($opcost[$i] as $j => $item)
                                         @if($j <= 30)
                                        <td style="border: 1px solid black; text-align: center; writing-mode: vertical-rl; transform: rotate(180deg);">
                                           {{ formatCost2($item) }}
                                        </td>
                                        @endif
                                        @endforeach    
                                                               {{-- <td
                                        style="border: 1px solid black; text-align: center; writing-mode: vertical-rl; transform: rotate(180deg);">
                                         formatCostTable(item.MeCost) 
                                    </td>  --}}
                                </tr>
                          
                                
                            </tbody>
                        </table>

                    </div>
                </div>
        @endfor
    </body>
</html>
