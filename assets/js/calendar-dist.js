var e=new Date,t=e.getMonth(),n=e.getFullYear(),a=["Janvier","Février","Mars","Avril","Mai","Juin","Juillet","Aout","Septembre","Octobre","Novembre","Decembre"];let d;function l(t,n){loadEvents();let l=(new Date(n,t).getDay()-1+7)%7,r=32-new Date(n,t,32).getDate(),i=document.getElementById("calendar-body");i.innerHTML="",d.innerHTML=a[t]+" "+n;let c=1;for(let a=0;a<6&&!(c>r);a++){let d=document.createElement("div");d.classList.add("d-flex","w-100");for(let i=0;i<7;i++){let o=document.createElement("div");if(o.classList.add("d-flex","border","rounded-0","flex-fill","w-100","justify-content-center","pt-3","pb-3"),0===a&&i<l||c>r){let e=document.createTextNode("");o.appendChild(e),d.appendChild(o)}else{let a=document.createTextNode(c);o.setAttribute("data-date",("0"+c).slice(-2)+"/"+("0"+(t+1)).slice(-2)+"/"+n),c===e.getDate()&&n===e.getFullYear()&&t===e.getMonth()&&o.classList.add("bg-info"),eventOnDay(new Date(n,t,c,0,UTCOffset,0),jsonDates)&&o.classList.add("bg-danger"),o.appendChild(a),d.appendChild(o),c++}}i.appendChild(d)}}$(".calendar").load(url_ajax_event+"includes/calendar.html",(function(){d=document.getElementById("monthAndYear"),l(t,n)}));