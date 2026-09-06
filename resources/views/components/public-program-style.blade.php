<style>
.kpv-page{
 min-height:100vh;
 padding:34px 0 55px;
 background:
 radial-gradient(circle at 90% 5%,rgba(17,197,189,.13),transparent 28%),
 #f3f7fc
}
.kpv-shell{
 width:min(1120px,94%);
 margin:auto;
 background:#fff;
 border:1px solid #dce7f4;
 border-radius:25px;
 overflow:hidden;
 box-shadow:0 18px 55px rgba(6,43,99,.09)
}
.kpv-brandbar{
 min-height:82px;
 padding:12px 22px;
 display:flex;
 align-items:center;
 justify-content:space-between;
 gap:18px;
 background:linear-gradient(135deg,#031b3f,#073b78);
 color:#fff
}
.kpv-brand,.kpv-mci{
 display:flex;
 align-items:center;
 gap:11px
}
.kpv-mci{text-align:right}
.kpv-brand img,.kpv-mci img{
 width:56px;height:56px;
 border-radius:50%;
 object-fit:contain;
 background:#fff;
 padding:3px
}
.kpv-brand strong,.kpv-brand small,
.kpv-mci strong,.kpv-mci small{
 display:block
}
.kpv-brand small,.kpv-mci small{
 color:#c9dbef;
 font-size:11px;
 margin-top:3px
}
.kpv-program{
 padding:30px 30px 25px;
 color:#fff;
 background:linear-gradient(135deg,#062b63,#0b478c 62%,#087f7a)
}
.kpv-eye{
 display:inline-block;
 padding:6px 10px;
 border:1px solid rgba(255,255,255,.28);
 border-radius:999px;
 font-size:10px;
 font-weight:900;
 letter-spacing:1px;
 background:rgba(255,255,255,.08)
}
.kpv-program h1{
 margin:11px 0 7px;
 font-size:clamp(27px,4vw,40px)
}
.kpv-program h1 b{color:#6ffff2}
.kpv-program p{
 margin:0;
 color:#d9eaff;
 line-height:1.6
}
.kpv-program-stats{
 display:grid;
 grid-template-columns:repeat(4,1fr);
 gap:10px;
 margin-top:20px
}
.kpv-program-stats div{
 padding:12px;
 border-radius:13px;
 background:rgba(255,255,255,.1);
 border:1px solid rgba(255,255,255,.13)
}
.kpv-program-stats strong,
.kpv-program-stats span{
 display:block
}
.kpv-program-stats strong{font-size:19px}
.kpv-program-stats span{
 color:#d4e7fa;
 font-size:10px;
 margin-top:3px
}
.kpv-modules{
 display:grid;
 grid-template-columns:repeat(4,1fr);
 gap:12px;
 padding:20px 25px;
 background:#f7fbff;
 border-bottom:1px solid #dce7f4
}
.kpv-modules div{
 padding:13px;
 background:#fff;
 border:1px solid #dce7f4;
 border-radius:13px
}
.kpv-modules b{
 display:block;
 color:#057d78;
 font-size:11px
}
.kpv-modules strong{
 display:block;
 color:#062b63;
 font-size:12px;
 margin:4px 0
}
.kpv-modules span{
 color:#718198;
 font-size:10px
}
.kpv-body{padding:28px}
.kpv-section{
 margin-top:25px;
 padding-top:22px;
 border-top:1px solid #e4edf6
}
.kpv-section:first-child{
 margin-top:0;
 padding-top:0;
 border-top:0
}
.kpv-section h2{
 color:#062b63;
 margin:0 0 5px;
 font-size:18px
}
.kpv-section>p{
 color:#718198;
 margin:0 0 17px;
 font-size:12px
}
.kpv-grid{
 display:grid;
 grid-template-columns:repeat(2,minmax(0,1fr));
 gap:15px
}
.kpv-field.full{grid-column:1/-1}
.kpv-field label{
 display:block;
 margin-bottom:6px;
 color:#26364f;
 font-weight:750;
 font-size:13px
}
.kpv-field input,
.kpv-field select,
.kpv-field textarea{
 width:100%;
 padding:12px 13px;
 border:1px solid #cbd8e8;
 border-radius:11px;
 font:inherit;
 background:#fff
}
.kpv-field textarea{min-height:95px}
.kpv-field input:focus,
.kpv-field select:focus,
.kpv-field textarea:focus{
 outline:3px solid rgba(17,197,189,.14);
 border-color:#11a9a3
}
.kpv-selected{
 padding:14px 16px;
 border-radius:13px;
 border:1px solid #9eddd6;
 background:#effffd;
 color:#075f5b
}
.kpv-selected strong{color:#062b63}
.kpv-actions{
 display:flex;
 gap:11px;
 flex-wrap:wrap;
 margin-top:26px
}
.kpv-errors{
 margin-bottom:20px;
 padding:14px 16px;
 border:1px solid #f1b7b1;
 background:#fff0ee;
 color:#a12a20;
 border-radius:13px
}
@media(max-width:800px){
 .kpv-program-stats,.kpv-modules{
  grid-template-columns:repeat(2,1fr)
 }
}
@media(max-width:620px){
 .kpv-brandbar{
  align-items:flex-start
 }
 .kpv-mci>div{display:none}
 .kpv-grid{grid-template-columns:1fr}
 .kpv-field.full{grid-column:auto}
 .kpv-body{padding:19px}
 .kpv-program{padding:24px 19px}
 .kpv-modules{padding:15px}
}
</style>
