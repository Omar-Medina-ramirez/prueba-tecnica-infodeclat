CREATE TABLE IF NOT EXISTS vendedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

CREATE TABLE IF NOT EXISTS ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendedor_id INT NOT NULL,
    fecha DATE NOT NULL,
    valor DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (vendedor_id) REFERENCES vendedores(id)
);

CREATE TABLE IF NOT EXISTS devoluciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendedor_id INT NOT NULL,
    fecha DATE NOT NULL,
    valor DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (vendedor_id) REFERENCES vendedores(id)
);

CREATE TABLE IF NOT EXISTS comisiones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vendedor_id INT NOT NULL,
    mes INT NOT NULL,
    ano INT NOT NULL,
    ventas_total DECIMAL(15,2) NOT NULL,
    devoluciones_total DECIMAL(15,2) NOT NULL,
    comision_base DECIMAL(15,2) NOT NULL,
    bono DECIMAL(15,2) DEFAULT 0,
    penalizacion DECIMAL(15,2) DEFAULT 0,
    comision_final DECIMAL(15,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendedor_id) REFERENCES vendedores(id)
);
