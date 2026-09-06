<style>
.kdoc-page{
 width:210mm;
 min-height:297mm;
 margin:20px auto;
 background:#fff;
 color:#17243c;
 padding:10mm;
 box-sizing:border-box;
 font-family:Arial,Helvetica,sans-serif
}
.kdoc-frame{
 position:relative;
 min-height:277mm;
 border:3px solid #06386e;
 border-radius:14px;
 padding:5mm;
 box-sizing:border-box;
 background:
 repeating-linear-gradient(
  -25deg,
  rgba(6,43,99,.018) 0,
  rgba(6,43,99,.018) 1px,
  transparent 1px,
  transparent 28px
 )
}
.kdoc-inner{
 min-height:265mm;
 border:1px solid #d8b74d;
 border-radius:11px;
 padding:7mm;
 position:relative
}
.kdoc-head{
 display:grid;
 grid-template-columns:35mm 1fr 35mm;
 gap:5mm;
 align-items:start
}
.kdoc-logo{
 width:32mm;
 height:32mm;
 object-fit:contain;
 display:block;
 margin:auto
}
.kdoc-title{text-align:center}
.kdoc-title h1{
 margin:1mm 0 0;
 color:#06386e;
 font-size:25pt;
 letter-spacing:1px
}
.kdoc-title h2{
 margin:2mm 0;
 color:#0a9c91;
 font-size:12pt
}
.kdoc-title p{
 color:#c38b15;
 font-size:8pt;
 font-weight:800;
 letter-spacing:.5px
}
.kdoc-meta{
 display:flex;
 justify-content:space-between;
 margin-top:2mm;
 font-size:7.5pt;
 font-weight:800;
 color:#26364f
}
.kdoc-student{
 text-align:center;
 margin-top:7mm
}
.kdoc-student .intro{
 color:#6c7789;
 font-size:9pt
}
.kdoc-student h3{
 color:#0a9c91;
 font-size:23pt;
 margin:2mm 0;
 text-transform:uppercase
}
.kdoc-line{
 width:58%;
 margin:0 auto 2mm;
 border-bottom:1px solid #d8b74d
}
.kdoc-student p{
 margin:1.5mm 0;
 font-size:9pt
}
.kdoc-org{
 color:#06386e;
 font-weight:900;
 font-size:12pt!important
}
.kdoc-trust{
 color:#6c7789;
 font-size:7.5pt!important
}
.kdoc-performance{
 margin-top:6mm;
 padding:4mm;
 background:#f3fbfb;
 border:1px solid #cfe7e6;
 border-radius:10px
}
.kdoc-performance-title{
 text-align:center;
 color:#06386e;
 font-weight:900;
 font-size:8pt;
 margin-bottom:4mm
}
.kdoc-cards{
 display:grid;
 grid-template-columns:repeat(3,1fr);
 gap:3mm
}
.kdoc-card{
 background:#fff;
 border:1px solid #dce5ec;
 border-radius:8px;
 text-align:center;
 overflow:hidden
}
.kdoc-card:before{
 content:"";
 display:block;
 height:3mm;
 background:#0a9c91
}
.kdoc-card:nth-child(2):before{background:#06386e}
.kdoc-card:nth-child(3):before{background:#9c36aa}
.kdoc-card b{
 display:block;
 font-size:19pt;
 color:#0a9c91;
 margin-top:3mm
}
.kdoc-card:nth-child(2) b{color:#06386e}
.kdoc-card:nth-child(3) b{color:#9c36aa}
.kdoc-card span{
 display:block;
 font-size:7.5pt;
 color:#68768a
}
.kdoc-card strong{
 display:block;
 font-size:16pt;
 margin:2mm 0 4mm;
 color:#17243c
}
.kdoc-ai{
 width:80%;
 margin:5mm auto 0;
 padding:3mm;
 border:1px solid #d8b74d;
 border-radius:999px;
 text-align:center;
 color:#06386e;
 font-weight:900;
 font-size:8pt;
 background:#fffdf5
}
.kdoc-summary{
 text-align:center;
 margin-top:5mm
}
.kdoc-summary small{color:#7b8797}
.kdoc-summary strong{
 display:block;
 margin-top:2mm;
 color:#0a9c91;
 font-size:13pt
}
.kdoc-signatures{
 display:grid;
 grid-template-columns:1fr 36mm 1fr;
 gap:8mm;
 align-items:end;
 margin-top:8mm
}
.kdoc-sign{
 border-top:1px solid #52627a;
 padding-top:2mm;
 text-align:center;
 font-size:7pt
}
.kdoc-seal{
 height:28mm;
 border:1px dashed #b8c5d3;
 border-radius:50%;
 display:grid;
 place-items:center;
 text-align:center;
 font-size:6pt;
 color:#78879a
}
.kdoc-qr{
 text-align:center;
 margin-top:5mm
}
.kdoc-qr img{
 width:25mm;
 height:25mm
}
.kdoc-qr b{
 display:block;
 font-size:7pt;
 margin-top:1mm
}
.kdoc-verify{
 font-size:6.5pt;
 color:#64748b;
 word-break:break-all
}
.kdoc-note{
 text-align:center;
 margin-top:2mm;
 color:#718198;
 font-size:6pt
}
.kdoc-actions{
 text-align:center;
 margin:15px auto 30px
}

/* Marksheet */
.kmark-title{
 text-align:center;
 margin:6mm 0
}
.kmark-title h1{
 color:#06386e;
 margin:0;
 font-size:22pt
}
.kmark-title h2{
 color:#0a9c91;
 margin:2mm 0;
 font-size:12pt
}
.kmark-info{
 display:grid;
 grid-template-columns:1fr 1fr;
 gap:2mm 8mm;
 padding:4mm;
 background:#f6faff;
 border:1px solid #dce7f4;
 border-radius:8px;
 font-size:8pt
}
.kmark-table{
 width:100%;
 border-collapse:collapse;
 margin-top:5mm;
 font-size:8pt
}
.kmark-table th,
.kmark-table td{
 border:1px solid #cfdae6;
 padding:3mm;
 text-align:center
}
.kmark-table th{
 background:#06386e;
 color:#fff
}
.kmark-table td:first-child{text-align:left}
.kmark-total{
 margin-top:5mm;
 display:grid;
 grid-template-columns:repeat(3,1fr);
 gap:3mm
}
.kmark-total div{
 text-align:center;
 padding:4mm;
 border-radius:8px;
 background:#f3fbfb;
 border:1px solid #cfe7e6
}
.kmark-total span{
 display:block;
 font-size:7pt;
 color:#718198
}
.kmark-total strong{
 display:block;
 color:#06386e;
 font-size:14pt;
 margin-top:1mm
}

@page{
 size:A4 portrait;
 margin:0
}
@media print{
 body{background:#fff!important}
 body>*{visibility:hidden}
 .kdoc-page,.kdoc-page *{visibility:visible}
 .kdoc-page{
  position:absolute;
  left:0;top:0;
  margin:0;
  width:210mm;
  min-height:297mm
 }
 .kdoc-actions{display:none!important}
}
@media(max-width:850px){
 .kdoc-page{
  width:100%;
  min-height:auto;
  padding:10px
 }
 .kdoc-frame,.kdoc-inner{min-height:auto}
}
</style>

<style>
.kdoc-trust-logo{
 width:36mm;
 height:30mm;
 margin:auto;
 display:flex;
 align-items:center;
 justify-content:center;
 overflow:hidden;
 background:#fff;
}

.kdoc-trust-logo img{
 width:100%;
 height:100%;
 display:block;
 object-fit:contain;
}
</style>

<style>
/* Final A4 document fitting */
@media print {

 .kdoc-page{
   width:210mm !important;
   height:297mm !important;
   min-height:297mm !important;
   padding:6mm !important;
   overflow:hidden !important;
   page-break-after:avoid !important;
   break-after:avoid-page !important;
 }

 .kdoc-frame{
   height:285mm !important;
   min-height:285mm !important;
   padding:4mm !important;
 }

 .kdoc-inner{
   height:273mm !important;
   min-height:273mm !important;
   padding:5mm !important;
   box-sizing:border-box !important;
 }

 .kmark-title{
   margin:3mm 0 !important;
 }

 .kmark-info{
   padding:3mm !important;
 }

 .kmark-table{
   margin-top:3mm !important;
 }

 .kmark-table th,
 .kmark-table td{
   padding:2.2mm !important;
 }

 .kmark-total{
   margin-top:3mm !important;
 }

 .kmark-total div{
   padding:3mm !important;
 }

 .kdoc-ai{
   margin-top:3mm !important;
   padding:2.2mm !important;
 }

 .kdoc-signatures{
   margin-top:5mm !important;
 }

 .kdoc-trust-logo{
   width:30mm !important;
   height:25mm !important;
 }

 .kdoc-qr{
   margin-top:3mm !important;
 }

 .kdoc-qr img{
   width:21mm !important;
   height:21mm !important;
 }

 .kdoc-note{
   margin-top:1mm !important;
   font-size:5.5pt !important;
 }

}

</style>
