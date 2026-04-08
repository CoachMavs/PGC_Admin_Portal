<template>
  <h1 class="card-header pb-3">Export to Excel Repair logs</h1>

  <div class="card">
    <v-progress-linear color="teal" indeterminate v-if="fetchLoading"></v-progress-linear>
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center">
        <v-row rows="auto">
          <!-- Date From -->

          <v-col cols="12" md="6">
            <v-menu
              v-model="menufrom"
              :close-on-content-click="false"
              transition="scale-transition"
              min-width="auto"
            >
              <template v-slot:activator="{ props }">
                <v-text-field
                  v-model="formattedDateFrom"
                  label="From"
                  append-inner-icon="mdi-calendar"
                  readonly
                  v-bind="props"
                  class="date-picker-field"
                  @input="handleDateInput"
                ></v-text-field>
              </template>

              <v-card class="d-flex justify-center align-center" style="width: 320px">
                <v-date-picker
                  v-model="datefrom"
                  hide-header
                  @update:model-value="
                    (value) => {
                      datefrom = value;
                      updateFormattedDateFrom();
                      menufrom = false; // Close the menu after selecting a date
                    }
                  "
                ></v-date-picker>
              </v-card>
            </v-menu>
          </v-col>

          <!-- Date To -->
          <v-col cols="12" md="6">
            <v-menu
              v-model="menuto"
              :close-on-content-click="false"
              transition="scale-transition"
              min-width="auto"
            >
              <template v-slot:activator="{ props }">
                <v-text-field
                  style="justify-content: center"
                  v-model="formattedDateTo"
                  label="To"
                  append-inner-icon="mdi-calendar"
                  readonly
                  v-bind="props"
                  class="date-picker-field"
                ></v-text-field>
              </template>

              <v-card class="d-flex justify-center align-center" style="width: 320px">
                <v-date-picker
                  v-model="dateto"
                  hide-header
                  @update:model-value="
                    (value) => {
                      dateto = value;
                      updateFormattedDateTo();
                      menuto = false; // Close the menu after selecting a date
                    }
                  "
                ></v-date-picker>
              </v-card>
            </v-menu>
          </v-col>
        </v-row>
      </div>
      <v-card-actions>
        <v-spacer></v-spacer>
        <v-btn
          color="#14727a"
          variant="elevated"
          style="text-transform: none"
          @click="fetch()"
          :loading="btnLoading"
          type="submit"
        >
          Export
        </v-btn>
      </v-card-actions>
    </div>
  </div>

  <MySnackBar ref="MySnackBar" />
</template>

<script>
/* eslint-disable */

import axios from "axios";
import "bootstrap";
import { format } from "date-fns";
import MySnackBar from "@/components/MySnackBar.vue";
import ExcelJS from "exceljs";

export default {
  name: "ExcelToFilter",
  components: {
    MySnackBar,
  },
  data: () => ({
    datefrom: null, // Initialize as null
    dateto: null, // Initialize as null
    formattedDateFrom: "",
    formattedDateTo: "",

    menufrom: false,
    menuto: false,
    fetchLoading: false,
    btnLoading: false,
    items: [],
  }),

  mounted() {
    this.dateto = new Date();
    this.formattedDateTo = this.formatDate(this.dateto);

    // Set "Date From" as 15 days before today
    let pastDate = new Date();
    pastDate.setDate(1); // Set to first day of month
    this.datefrom = pastDate;
    this.formattedDateFrom = this.formatDate(this.datefrom);
  },

  methods: {
    formatDateTable(date) {
      return format(date, "MMM dd, yyyy hh:mm a");
    },
    formatDate(date) {
      return format(date, "MMM-dd-yyyy");
    },
    updateFormattedDateFrom() {
      this.formattedDateFrom = this.datefrom ? this.formatDate(this.datefrom) : "";
    },
    updateFormattedDateTo() {
      this.formattedDateTo = this.dateto ? this.formatDate(this.dateto) : "";
    },

    fetch(paramType = null) {
      let myParameter = {
        datefrom: this.formatDate(this.datefrom),
        dateto: this.formatDate(this.dateto),
      };

      let loadData = () => {
        this.fetchLoading = true;
        axios({
          method: "get",
          url: process.env.VUE_APP_API + "ExportToPDF/fetchRepairs",
          headers: {
            Authorization: "Bearer " + localStorage.getItem("xxx"),
          },
          params: myParameter,
        })
          .then((resp) => {
            this.items = resp.data;
            console.log(this.items);
            this.fetchLoading = false;
            this.exportToExcel();
          })
          .catch((err) => {
            this.fetchLoading = false;
            let errorMsg =
              err?.response?.data?.message || err?.message || JSON.stringify(err);
            this.$refs.MySnackBar.showErrorMessage("Something went wrong!", errorMsg);
          });
      };

      if (paramType == null) {
        myParameter = {
          datefrom: this.formatDate(this.datefrom),
          dateto: this.formatDate(this.dateto),
        };
        loadData();
      } else if (paramType == "page") {
        myParameter = {
          datefrom: this.formatDate(this.datefrom),
          dateto: this.formatDate(this.dateto),
        };
        loadData();
      } else if (paramType == "search") {
        myParameter = {
          datefrom: this.formatDate(this.datefrom),
          dateto: this.formatDate(this.dateto),
        };
        loadData();
      }
    },
    // ...existing code...
    async exportToExcel() {
      this.btnLoading = true;

      try {
        const jsonData = this.items;

        const workbook = new ExcelJS.Workbook();
        const worksheet = workbook.addWorksheet("Sheet1");

        // ① Add title row
        const title = `Previous Repairs from ${this.formattedDateFrom} to ${this.formattedDateTo}`;
        const titleRow = worksheet.addRow([title]);

        // Style the title row: bold, larger font
        titleRow.font = {
          name: "Arial",
          size: 14,
          bold: true,
        };

        // Add an empty row for spacing (optional)
        worksheet.addRow([]);

        // ② Add header row
        const headers = Object.keys(jsonData[0] || {});
        const headerRow = worksheet.addRow(headers);

        // Style the header row: bold
        headerRow.font = {
          bold: true,
        };

        // Optional: set header row background color
        headerRow.eachCell((cell) => {
          cell.fill = {
            type: "pattern",
            pattern: "solid",
            fgColor: { argb: "FFEFEFEF" },
          };
        });

        // ③ Add data rows
        jsonData.forEach((item) => {
          worksheet.addRow(Object.values(item));
        });

        // Auto-fit column widths except the title column (row 1)
        // The title row is a single merged cell, so skip it
        // Header is row 3, data starts at row 4
        // worksheet.columns aligns with headers/data, not the title

        worksheet.columns.forEach((column, colIdx) => {
          let maxLength = 10;
          // Start from header row (row 3) and include all data rows
          for (let rowIdx = 3; rowIdx <= worksheet.rowCount; rowIdx++) {
            const cell = worksheet.getRow(rowIdx).getCell(colIdx + 1);
            const cellValue = cell.value ? cell.value.toString() : "";
            maxLength = Math.max(maxLength, cellValue.length);
          }
          column.width = maxLength + 2;
        });

        // Add border to all cells except the title row (row 1)
        for (let i = 2; i <= worksheet.rowCount; i++) {
          // start from row 2 (empty row), so all table rows get border
          const row = worksheet.getRow(i);
          row.eachCell({ includeEmpty: true }, (cell) => {
            cell.border = {
              top: { style: "thin" },
              left: { style: "thin" },
              bottom: { style: "thin" },
              right: { style: "thin" },
            };
          });
        }

        // Generate buffer and trigger download
        const buffer = await workbook.xlsx.writeBuffer();

        const blob = new Blob([buffer], {
          type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
        });

        const url = window.URL.createObjectURL(blob);
        const anchor = document.createElement("a");
        anchor.href = url;
        anchor.download = "Repair logs.xlsx";
        anchor.click();
        window.URL.revokeObjectURL(url);
      } catch (error) {
        this.$refs.MySnackBar.showErrorMessage("Export failed", error.message);
      } finally {
        this.btnLoading = false;
      }
    },
    // ...existing
  },
};
</script>

<style scoped>
.table-responsive {
  overflow-x: auto;
}

.date-picker-field {
  min-width: 200px; /* Adjust width as needed */
  overflow: visible; /* Ensure text is not clipped */
}

.v-divider {
  background-color: #e0e0e0;
  height: 2px;
  margin: 5px 0;
}

thead th {
  vertical-align: middle;
  background-color: #303847;
  color: white;
}
</style>
