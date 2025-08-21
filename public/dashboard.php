<!DOCTYPE html>
<html>
<head>
  <title>Dashboard VentasPlus BI</title>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
  <h1>Dashboard Comisiones</h1>
  <canvas id="top5"></canvas>
  <canvas id="comisionesMes"></canvas>
  <canvas id="bonos"></canvas>

  <script>
    async function loadData() {
      const res = await fetch('/api/v1/comisiones.php?mes=<?php echo date("n"); ?>&ano=<?php echo date("Y"); ?>');
      const json = await res.json();
      const data = json.data;

      const top = [...data].sort((a,b)=>b.comision_final - a.comision_final).slice(0,5);
      new Chart(document.getElementById('top5'), {
        type: 'bar',
        data: {
          labels: top.map(x=>x.vendedor),
          datasets: [{label:'Comisión Final', data: top.map(x=>x.comision_final)}]
        }
      });

      new Chart(document.getElementById('comisionesMes'), {
        type: 'doughnut',
        data: {
          labels: data.map(x=>x.vendedor),
          datasets: [{label:'Comisión', data: data.map(x=>x.comision_final)}]
        }
      });

      const conBono = data.filter(x=>x.bono > 0).length;
      const total = data.length;
      new Chart(document.getElementById('bonos'), {
        type: 'pie',
        data: {
          labels: ['Con Bono','Sin Bono'],
          datasets: [{data:[conBono, total-conBono]}]
        }
      });
    }
    loadData();
  </script>
</body>
</html>