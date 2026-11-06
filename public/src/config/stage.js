export default {
  "name": "graduasi_kpm",
  "version": 1,
  "stages": [
    {
      "id": "stage_1",
      "permission": "pendamping",
      "form": {
        "sections": [
          {
            "label": "Pemeriksaan Data",
            "fields": [
              {
                "name": "identity",
                "label": "Identitas",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "social_assistance",
                "label": "Kepesertaan Bansos",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "family_condition",
                "label": "Kondisi Keluarga",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "business_profile",
                "label": "Profil Usaha",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "income",
                "label": "Pendapatan",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "documents",
                "label": "Dokumen Pendukung",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              }
            ]
          },
          {
            "label": "Hasil",
            "fields": [
              {
                "name": "results",
                "label": "Hasil",
                "type": "radio",
                "options": ["Sesuai", "Belum Sesuai","Jadwalkan Kunjungan"]
              },
              {
                "name": "notes",
                "label": "Catatan",
                "type": "textarea"
              }
            ]
          }
        ]
      },
      "actions": [
        {
          "action": "sendTo",
          "target": "stage_2",
          "when": {
            "field": "results",
            "equals": "Sesuai"
          }
        },
        {
          "action": "reject",
          "when": {
            "field": "results",
            "equals": "Belum Sesuai"
          }
        }
      ]
    },

    {
      "id": "stage_2",
      "permission": "kecamatan",
      "form": {
        "sections": [
          {
            "label": "Pemeriksaan Administratif",
            "fields": [
              {
                "name": "profile",
                "label": "Kelengkapan Profile",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "verification_result",
                "label": "Hasil Verifikasi Pendamping",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "documents",
                "label": "Dokumen Pendukung",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "duplication",
                "label": "Duplikasi Data",
                "type": "radio",
                "options": ["Duplikat", "Tidak Duplikat"]
              }
            ]
          },
          {
            "label": "Hasil",
            "fields": [
              {
                "name": "results",
                "label": "Hasil",
                "type": "radio",
                "options": ["Sesuai", "Belum Sesuai"]
              },
              {
                "name": "notes",
                "label": "Catatan",
                "type": "textarea"
              }
            ]
          }
        ]
      },
      "actions": [
        {
          "action": "sendTo",
          "target": "stage_3",
          "when": {
            "field": "results",
            "equals": "Sesuai"
          }
        },
        {
          "action": "sendTo",
          "target": "stage_1",
          "when": {
            "field": "results",
            "equals": "Belum Sesuai"
          }
        }
      ]
    },

    {
      "id": "stage_3",
      "permission": "dinsos",
      "form": {
        "sections": [
          {
            "label": "Penelaahan Data",
            "fields": [
              {
                "name": "identity",
                "label": "Identitas",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "social_assistance",
                "label": "Status DTSEN dan Bansos",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "profile",
                "label": "Kelengkapan Profil",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "verification",
                "label": "Hasil Verifikasi",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "duplication",
                "label": "Duplikasi Data",
                "type": "radio",
                "options": ["Duplikat", "Tidak Duplikat"]
              }
            ]
          },
          {
            "label": "Hasil",
            "fields": [
              {
                "name": "results",
                "label": "Hasil",
                "type": "radio",
                "options": ["Sesuai", "Belum Sesuai"]
              },
              {
                "name": "notes",
                "label": "Catatan",
                "type": "textarea"
              }
            ]
          }
        ]
      },
      "actions": [
        {
          "action": "sendTo",
          "target": "stage_1",
          "when": {
            "field": "results",
            "equals": "Belum Sesuai"
          }
        },
        {
          "action": "sendTo",
          "target": "stage_4",
          "when": {
            "field": "results",
            "equals": "Sesuai"
          },
          "effects": [
            {
              "action": "generateId",
              "name": "astabrata"
            }
          ]
        }
      ]
    },

    {
      "id": "stage_4",
      "permission": ["asesor","dinsos"],
      "form": {
        "sections": [
          {
            "label": "Verifikasi Kondisi Keluarga",
            "fields": [
              {
                "name": "identity",
                "label": "Identitas",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "family",
                "label": "Anggota Keluarga",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "profession",
                "label": "Pekerjaan",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "revenue",
                "label": "Penghasilan",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "home_condition",
                "label": "Kondisi Tempat Tinggal",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              }
            ]
          },
          {
            "label": "Penilaian Kemandirian",
            "fields": [
              {
                "name": "economy",
                "label": "Ekonomi",
                "type": "radio",
                "options": ["Memenuhi", "Belum Memenuhi"]
              },
              {
                "name": "social",
                "label": "Sosial",
                "type": "radio",
                "options": ["Memenuhi", "Belum Memenuhi"]
              },
              {
                "name": "education",
                "label": "Pendidikan",
                "type": "radio",
                "options": ["Memenuhi", "Belum Memenuhi"]
              },
              {
                "name": "health",
                "label": "Kesehatan",
                "type": "radio",
                "options": ["Memenuhi", "Belum Memenuhi"]
              },
              {
                "name": "home",
                "label": "Tempat Tinggal",
                "type": "radio",
                "options": ["Memenuhi", "Belum Memenuhi"]
              },
              {
                "name": "sanitation",
                "label": "Air Bersih dan Sanitasi",
                "type": "radio",
                "options": ["Memenuhi", "Belum Memenuhi"]
              }
            ]
          },
          {
            "label": "Kesimpulan Asesmen",
            "fields": [
              {
                "name": "results",
                "label": "Kesimpulan",
                "type": "radio",
                "options": [
                  "Memenuhi Kriteria Mandiri",
                  "Belum Memenuhi",
                  "Memerlukan Verifikasi Lanjutan"
                ]
              },
              {
                "name": "notes",
                "label": "Catatan",
                "type": "textarea"
              }
            ]
          }
        ]
      },
      "actions": [
        {
          "action": "sendTo",
          "target": "stage_5",
          "when": {
            "field": "results",
            "equals": "Memenuhi Kriteria Mandiri"
          }
        }
      ]
    },

    {
      "id": "stage_5",
      "permission": "opd",
      "form": {
        "sections": [
          {
            "label": "Rujukan Masuk",
            "fields": [
              {
                "name": "analysis",
                "label": "Telaah Kebutuhan",
                "type": "textarea"
              },
              {
                "name": "program",
                "label": "Program Kegiatan",
                "type": "text"
              },
              {
                "name": "assign_to",
                "label": "Petugas",
                "type": "text"
              },
              {
                "name": "schedule",
                "label": "Jadwal",
                "type": "date"
              }
            ]
          },
          {
            "label": "Hasil",
            "fields": [
              {
                "name": "results",
                "label": "Hasil",
                "type": "radio",
                "options": ["Jadwalkan", "Draft"]
              },
              {
                "name": "notes",
                "label": "Catatan",
                "type": "textarea"
              }
            ]
          }
        ]
      },
      "actions": [
        {
          "action": "sendTo",
          "target": "stage_6",
          "when": {
            "field": "results",
            "equals": "Jadwalkan"
          }
        }
      ]
    },

    {
      "id": "stage_6",
      "permission": "opd",
      "form": {
        "sections": [
          {
            "label": "Realisasi Intervensi",
            "fields": [
              {
                "name": "implementation_date",
                "label": "Tanggal Pelaksanaan",
                "type": "date"
              },
              {
                "name": "initial_result",
                "label": "Hasil Awal",
                "type": "textarea"
              },
              {
                "name": "documents",
                "label": "Bukti Layanan",
                "type": "file"
              },
              {
                "name": "problem",
                "label": "Kendala",
                "type": "textarea"
              },
              {
                "name": "follow_up",
                "label": "Tindak Lanjut",
                "type": "textarea"
              }
            ]
          },
          {
            "label": "Hasil",
            "fields": [
              {
                "name": "results",
                "label": "Status Intervensi",
                "type": "radio",
                "options": ["Selesai", "Sedang Berjalan", "Draft"]
              },
              {
                "name": "notes",
                "label": "Catatan",
                "type": "textarea"
              }
            ]
          }
        ]
      },
      "actions": [
        {
          "action": "sendTo",
          "target": "stage_7",
          "when": {
            "field": "results",
            "equals": "Selesai"
          }
        }
      ]
    },

    {
      "id": "stage_7",
      "permission": "pelaksana",
      "form": {
        "sections": [
          {
            "label": "Penilaian Ulang Kemandirian",
            "fields": [
              {
                "name": "economy",
                "label": "Indikator Ekonomi",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "social",
                "label": "Indikator Sosial",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "family",
                "label": "Kondisi Keluarga",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "business",
                "label": "Keberlanjutan Usaha",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              }
            ]
          },
          {
            "label": "Verifikasi dan Konfirmasi",
            "fields": [
              {
                "name": "data_match",
                "label": "Data Sesuai",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "evidence",
                "label": "Bukti Lengkap",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "consistency",
                "label": "Konsistensi Informasi",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
            ]
          },
          {
            "label": "Kesimpulan Status",
            "fields": [
              {
                "name": "results",
                "label": "Rekomendasi Status KPM",
                "type": "radio",
                "options": [
                  "Layak direkomendasikan Graduasi",
                  "Belum Layak Graduasi",
                  "Memerlukan Verifikasi Lanjutan"
                ]
              },
              {
                "name": "notes",
                "label": "Catatan",
                "type": "textarea"
              }
            ]
          }
        ]
      },
      "actions": [
        {
          "action": "sendTo",
          "target": "stage_8",
          "when": {
            "field": "results",
            "equals": "Layak direkomendasikan Graduasi"
          }
        }
      ]
    },

    {
      "id": "stage_8",
      "permission": "desa",
      "form": {
        "sections": [
          {
            "label": "Hasil Konfirmasi",
            "fields": [
              {
                "name": "berita_acara",
                "label": "Berita Acara",
                "type": "file"
              },
              {
                "name": "daftar_hadir",
                "label": "Daftar Hadir",
                "type": "file"
              },
              {
                "name": "rekomendasi_forum",
                "label": "Rekomendasi Forum",
                "type": "file"
              },
              {
                "name": "surat_pernyataan",
                "label": "Surat Pernyataan KPM",
                "type": "file"
              }
            ]
          },
          {
            "label": "Hasil",
            "fields": [
              {
                "name": "results",
                "label": "Hasil Forum Desa / Kelurahan",
                "type": "radio",
                "options": [
                  "Dapat Dilanjutkan",
                  "Belum Dapat Dilanjutkan",
                  "Verifikasi Kembali"
                ]
              },
              {
                "name": "notes",
                "label": "Catatan",
                "type": "textarea"
              }
            ]
          }
        ]
      },
      "actions": [
        {
          "action": "sendTo",
          "target": "stage_9",
          "when": {
            "field": "results",
            "equals": "Dapat Dilanjutkan"
          }
        }
      ]
    },

    {
      "id": "stage_9",
      "permission": "dinsos",
      "form": {
        "sections": [
          {
            "label": "Status KPM",
            "fields": [
              {
                "name": "results",
                "label": "Status KPM",
                "type": "radio",
                "options": [
                  "Graduasi Mandiri",
                  "Belum Graduasi"
                ]
              },
              {
                "name": "notes",
                "label": "Catatan",
                "type": "textarea"
              }
            ]
          }
        ]
      },
      "actions": [
        {
          "action": "sendTo",
          "target": "stage_10",
          "when": {
            "field": "results",
            "equals": "Graduasi Mandiri"
          }
        }
      ]
    },

    {
      "id": "stage_10",
      "permission": "kpm.stage_10",
      "form": {
        "sections": [
          {
            "label": "Verifikasi Proposal Lapangan",
            "fields": [
              {
                "name": "general",
                "label": "Data Umum",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "business_profile",
                "label": "Profil Usaha",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "assessment",
                "label": "Hasil Asesmen",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "problem_needed",
                "label": "Permasalahan & Kebutuhan",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "business_goal",
                "label": "Tujuan Usaha",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "budget",
                "label": "RAB dan Kebutuhan",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "training_plan",
                "label": "Rencana Pelatihan",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "income_estimation",
                "label": "Estimasi Pendapatan",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "documents",
                "label": "Dokumentasi",
                "type": "file"
              }
            ]
          },
          {
            "label": "Penilaian Teknis Anggaran",
            "fields": [
              {
                "name": "business_feasibility",
                "label": "Kelayakan Usaha",
                "type": "radio",
                "options": ["Layak", "Tidak Layak"]
              },
              {
                "name": "benefit",
                "label": "Manfaat terhadap Kemandirian",
                "type": "radio",
                "options": ["Sesuai", "Tidak Sesuai"]
              },
              {
                "name": "price_reasonable",
                "label": "Kewajaran Harga",
                "type": "radio",
                "options": ["Wajar", "Tidak Wajar"]
              },
              {
                "name": "duplication_risk",
                "label": "Risiko Duplikasi Bantuan",
                "type": "radio",
                "options": ["Berisiko", "Tidak Berisiko"]
              },
              {
                "name": "available_budget",
                "label": "Ketersediaan Anggaran",
                "type": "radio",
                "options": ["Tersedia", "Tidak Tersedia"]
              }
            ]
          },
          {
            "label": "Hasil",
            "fields": [
              {
                "name": "results",
                "label": "Keputusan Dukungan UEP",
                "type": "radio",
                "options": [
                  "Layak Menerima",
                  "Perlu Perbaikan",
                  "Belum Layak"
                ]
              },
              {
                "name": "notes",
                "label": "Catatan",
                "type": "textarea"
              }
            ]
          }
        ]
      },
      "actions": [
        {
          "action": "sendTo",
          "target": "stage_11",
          "when": {
            "field": "results",
            "equals": "Layak Menerima"
          }
        }
      ]
    },

    {
      "id": "stage_11",
      "permission": "kpm.stage_11",
      "form": {
        "sections": [
          {
            "label": "Penyerahan dan Pencatatan Bukti",
            "fields": [
              {
                "name": "date",
                "label": "Tanggal",
                "type": "date"
              },
              {
                "name": "address",
                "label": "Alamat",
                "type": "textarea"
              },
              {
                "name": "proof_paper",
                "label": "Berita Acara",
                "type": "file"
              },
              {
                "name": "documents",
                "label": "Dokumentasi",
                "type": "file"
              }
            ]
          },
          {
            "label": "Hasil",
            "fields": [
              {
                "name": "results",
                "label": "Status",
                "type": "radio",
                "options": ["Diterima", "Draft"]
              },
              {
                "name": "notes",
                "label": "Catatan",
                "type": "textarea"
              }
            ]
          }
        ]
      },
      "actions": [
        {
          "action": "sendTo",
          "target": "stage_12",
          "when": {
            "field": "results",
            "equals": "Diterima"
          }
        }
      ]
    },

    {
      "id": "stage_12",
      "permission": "kpm.stage_12",
      "form": {
        "sections": [
          {
            "label": "Pendampingan Pemanfaatan",
            "fields": [
              {
                "name": "training",
                "label": "Pelatihan Usaha",
                "type": "radio",
                "options": ["Selesai", "Belum Dilaksanakan"]
              },
              {
                "name": "operational_plan",
                "label": "Rencana Operasional",
                "type": "radio",
                "options": ["Tercatat", "Tidak Tercatat"]
              },
              {
                "name": "benefit",
                "label": "Pemanfaatan Sarana",
                "type": "radio",
                "options": ["Berjalan", "Tidak Berjalan"]
              },
              {
                "name": "production",
                "label": "Produksi Penjualan",
                "type": "radio",
                "options": ["Dicatat", "Tidak Dicatat"]
              },
              {
                "name": "problem",
                "label": "Kendala dan Solusi",
                "type": "radio",
                "options": ["Ditindaklanjuti", "Tidak Ditindaklanjuti"]
              }
            ]
          },
          {
            "label": "Hasil",
            "fields": [
              {
                "name": "results",
                "label": "Hasil",
                "type": "radio",
                "options": [
                  "Usaha Berkembang",
                  "Usaha Stabil",
                  "Memerlukan Pendampingan Lanjutan"
                ]
              },
              {
                "name": "notes",
                "label": "Catatan",
                "type": "textarea"
              }
            ]
          }
        ]
      }
    }
  ]
}