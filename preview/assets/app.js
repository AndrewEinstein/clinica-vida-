(function(){
  const patients = [
    {id:1,name:'João Silva',cpf:'123.456.789-00',phone:'(65) 99999-0001'},
    {id:2,name:'Maria Oliveira',cpf:'987.654.321-11',phone:'(65) 99999-0002'},
    {id:3,name:'Carlos Pereira',cpf:'111.222.333-44',phone:'(65) 99999-0003'},
  ];

  const appointments = [
    {time:'09:00',date:'2026-04-29',patient:'João Silva',doctor:'Dr. Paulo',type:'Consulta',status:'Agendada'},
    {time:'10:30',date:'2026-04-29',patient:'Maria Oliveira',doctor:'Dra. Helena',type:'Retorno',status:'Confirmada'},
    {time:'14:00',date:'2026-04-29',patient:'Carlos Pereira',doctor:'Dr. Marcos',type:'Exame',status:'Pendente'},
  ];

  function el(sel){return document.querySelector(sel)}
  function els(sel){return Array.from(document.querySelectorAll(sel))}

  // populate dashboard
  if(el('#m-patients')) el('#m-patients').textContent = patients.length;
  if(el('#m-appointments')) el('#m-appointments').textContent = appointments.length;
  if(el('#m-available')) el('#m-available').textContent = '5';

  if(el('#upcoming tbody')){
    const tbody = el('#upcoming tbody');
    appointments.forEach(a=>{
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${a.date} ${a.time}</td><td>${a.patient}</td><td>${a.doctor}</td><td>${a.type}</td>`;
      tbody.appendChild(tr);
    });
  }

  // patients table
  if(el('#patients-table tbody')){
    const tbody = el('#patients-table tbody');
    patients.forEach(p=>{
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${p.name}</td><td>${p.cpf}</td><td>${p.phone}</td><td><a href="medical-record.html?patient=${p.id}">Ver</a></td>`;
      tbody.appendChild(tr);
    });
  }

  // appointments table
  if(el('#appointments-table tbody')){
    const tbody = el('#appointments-table tbody');
    appointments.forEach(a=>{
      const tr = document.createElement('tr');
      tr.innerHTML = `<td>${a.date} ${a.time}</td><td>${a.patient}</td><td>${a.doctor}</td><td>${a.status}</td>`;
      tbody.appendChild(tr);
    });
  }

  // medical record page
  if(el('#patient-name')){
    const params = new URLSearchParams(location.search);
    const id = Number(params.get('patient')) || 1;
    const p = patients.find(x=>x.id===id) || patients[0];
    el('#patient-name').textContent = p.name;
    el('#patient-info').textContent = `${p.cpf} • Nascimento: 1990-01-01 • ${p.phone}`;
    const hist = el('#patient-history');
    ['Consulta (2026-04-01)','Retorno (2026-03-15)','Exame (2026-02-02)'].forEach(h=>{
      const li = document.createElement('li'); li.textContent = h; hist.appendChild(li);
    });
  }

})();