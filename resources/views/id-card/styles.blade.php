<style>
.id-screen-actions,.bulk-toolbar{
 display:flex;
 justify-content:center;
 gap:10px;
 flex-wrap:wrap;
 margin:0 auto 20px
}

.bulk-toolbar{
 padding:20px
}

.id-a4-sheet{
 width:210mm;
 min-height:297mm;
 margin:0 auto 22px;
 padding:12mm;
 box-sizing:border-box;
 background:#fff;
 display:flex;
 flex-direction:column;
 justify-content:flex-start;
 gap:11mm;
 box-shadow:0 6px 28px rgba(0,0,0,.08)
}

.id-student-unit{
 display:grid;
 grid-template-columns:1fr 1fr;
 gap:7mm;
 break-inside:avoid;
 page-break-inside:avoid
}

.id-side{
 width:85.6mm;
 height:54mm;
 box-sizing:border-box;
 overflow:hidden;
 border-radius:4mm;
 border:0.35mm solid #bdd0e4;
 background:#fff;
 position:relative;
 font-family:Arial,sans-serif
}

.id-topbar{
 height:14mm;
 box-sizing:border-box;
 display:flex;
 align-items:center;
 justify-content:space-between;
 padding:2mm 3mm;
 color:#fff;
 background:linear-gradient(135deg,#062b63,#087f7a)
}

.id-brand{
 display:flex;
 align-items:center;
 gap:2mm
}

.id-brand img{
 width:10mm;
 height:10mm;
 object-fit:contain;
 border-radius:50%;
 background:#fff;
 padding:.5mm
}

.id-brand strong,
.id-brand small,
.id-back-head strong,
.id-back-head small{
 display:block
}

.id-brand strong{
 font-size:8pt;
 letter-spacing:.2pt
}

.id-brand small{
 font-size:5.8pt;
 color:#d9f6ff;
 margin-top:.5mm
}

.id-mci{
 width:10mm;
 height:10mm;
 object-fit:contain;
 border-radius:50%;
 background:#fff;
 padding:.4mm
}

.id-front-body{
 height:34mm;
 box-sizing:border-box;
 display:grid;
 grid-template-columns:19mm 1fr 18mm;
 gap:2.5mm;
 padding:3mm
}

.id-photo{
 width:18mm;
 height:23mm;
 border-radius:2mm;
 overflow:hidden;
 background:#e9f2fb;
 display:flex;
 align-items:center;
 justify-content:center;
 color:#062b63;
 font-size:18pt;
 font-weight:900;
 border:.5mm solid #d2dfec
}

.id-photo img{
 width:100%;
 height:100%;
 object-fit:cover
}

.id-main-data{
 min-width:0
}

.id-main-data h3{
 margin:0 0 1mm;
 color:#062b63;
 font-size:9pt;
 line-height:1.1
}

.id-badge{
 display:inline-block;
 padding:.7mm 1.7mm;
 margin-bottom:1.5mm;
 border-radius:10mm;
 background:#e6fffb;
 color:#057d78;
 font-size:5.3pt;
 font-weight:900
}

.id-line{
 display:flex;
 justify-content:space-between;
 gap:2mm;
 border-bottom:.2mm solid #e3ebf3;
 padding:1mm 0;
 font-size:5.8pt;
 line-height:1.2
}

.id-line span{
 color:#687a91
}

.id-line strong{
 color:#24364e;
 text-align:right;
 overflow-wrap:anywhere
}

.id-qr{
 text-align:center
}

.id-qr img{
 width:16mm;
 height:16mm
}

.id-qr small{
 display:block;
 font-size:4.5pt;
 color:#52627a;
 margin-top:.5mm
}

.id-bottom{
 height:6mm;
 box-sizing:border-box;
 background:#f0f5fa;
 color:#53667e;
 display:flex;
 align-items:center;
 justify-content:center;
 text-align:center;
 font-size:5.2pt;
 font-weight:700
}


/* BACK */

.id-back-head{
 height:14mm;
 display:flex;
 align-items:center;
 gap:2mm;
 box-sizing:border-box;
 padding:2mm 3mm;
 background:linear-gradient(135deg,#062b63,#0b478c);
 color:#fff
}

.id-back-head img{
 width:10mm;
 height:10mm;
 object-fit:contain;
 background:#fff;
 border-radius:50%;
 padding:.5mm
}

.id-back-head strong{
 font-size:8pt
}

.id-back-head small{
 font-size:5.5pt;
 color:#d9e8fa
}

.id-back-body{
 height:34mm;
 box-sizing:border-box;
 padding:2.5mm 3mm;
 display:grid;
 grid-template-columns:1.05fr .95fr;
 column-gap:4mm;
 align-content:start
}

.id-back-data .id-line{
 font-size:5.5pt
}

.id-rules{
 font-size:5.2pt;
 color:#52627a;
 line-height:1.25
}

.id-rules strong{
 color:#062b63;
 font-size:6pt
}

.id-rules p{
 margin:.8mm 0
}

.id-address{
 grid-column:1/-1;
 margin-top:1.4mm;
 padding-top:1mm;
 border-top:.2mm solid #e1e9f2;
 font-size:5.1pt;
 color:#52627a;
 white-space:nowrap;
 overflow:hidden;
 text-overflow:ellipsis
}

.id-verification{
 grid-column:1/-1;
 margin-top:1mm;
 font-size:4.9pt;
 color:#718198
}

@media(max-width:900px){
 .id-a4-sheet{
   width:100%;
   min-height:auto;
   padding:15px
 }
 .id-student-unit{
   grid-template-columns:1fr
 }
 .id-side{
   width:min(100%,85.6mm);
   margin:auto
 }
}

@page{
 size:A4 portrait;
 margin:0
}

@media print{
 body{
   margin:0!important;
   background:#fff!important
 }

 .kps-top,
 .kps-side,
 .kps-head,
 .id-screen-actions,
 .bulk-toolbar{
   display:none!important
 }

 .kps-layout,
 .kps-main,
 .kps-content{
   display:block!important;
   width:100%!important;
   margin:0!important;
   padding:0!important;
   border:0!important;
   box-shadow:none!important
 }

 .id-a4-sheet{
   width:210mm!important;
   height:297mm!important;
   min-height:297mm!important;
   margin:0!important;
   padding:12mm!important;
   box-shadow:none!important;
   page-break-after:always;
   break-after:page
 }

 .id-a4-sheet:last-child{
   page-break-after:auto;
   break-after:auto
 }

 .id-student-unit{
   grid-template-columns:1fr 1fr!important;
   gap:7mm!important
 }
}
</style>
