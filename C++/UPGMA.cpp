/**
 * Este programa se va a utilizar para contar la frecuencia de aparición de las palabras de 3 letras
 */
#include <string>
#include <iostream>
#include <fstream>
#include <cmath>
#include <list>
#include <cstdlib>
#include <iomanip>
#include <math.h>
using namespace std;


// Representación de los fragementos de ADN
enum Letra { A=0, C, G, T};                     // < representación númerica para el índice de la tabla
int letras_nombre [4] = {'A', 'C', 'G', 'T'};   //< representación con caracteres para humanos

/**
 * Forma palabras almacenandolas en un buffer de 6bits, 2bits por letra.
 * El buffer va desplazando va metiendo y desplazando letras a la derecha.
 * Solo cuenta la letra si ya ha contado 2 antes.
 */
class Contador {
private:
    // CONSTANTES QUE SE PIDEN O CALCULAN AL PRINCIPIO DEL PROGRAMA //
    int LETRAS;        // k, se pide al principio de la ejcución
    int COMB_PALABRAS; // total de combinaciones que pueden aparecer (4^k)
    int MASCARA;       // mantiene constante el tamaño de la palabra (4^k-1)

    int caracteres;     // número de letras que lleva contadas sin error
    int* apariciones;   // array que cuenta la frecuencia de aparicion de las palabras
    int letras;         // letras que de la palabra que hay almacenada en el buffer (se reincia al error)
    int palabra;        // buffer 2*k bits que representan k letras de 2 bits 332211 (EJ k=3: ACT 0b11_01_00)

    int palabras;       // número de palabras totales que se han contado

public:
    
    /**
     * @param k Longitud de las palabras que se van a medir la frecuencia
     */
    Contador(int k) {
        LETRAS = k;
        COMB_PALABRAS = 1<<(k<<1); // 4^k
        MASCARA = COMB_PALABRAS-1;

        caracteres = 0;
        apariciones = new int [COMB_PALABRAS];
        reset();
    } 

    /**
     * Borra toda la memoria dinámica resevada 
     */
    ~Contador() {
        delete [] apariciones;
    }

    /**
     * Inserta una letra del buffer
     * @param codigo_letra Número de 2 bits que representa el una letra
     */
    void contar (const int codigo_letra) {
        // actualizar la palabra actual
        caracteres++;
        palabra <<= 2;
        palabra += codigo_letra;
        palabra &= MASCARA;

        if(letras >= LETRAS) { // Si esta es la kª letra
            apariciones [palabra] ++;
            palabras++;
        }
        else
            letras++;
    }

    /**
     * Devuelve un string a partir de un código de 6 bits que representa una palabra de 3 letras.
     * El ordena de las letras es inverso.
     * Por ejemplo: 0b00_11_01 -> "CTG"
     *              0b10_01_00 -> "ACG"
     * @param codigoPalabra Representaición númerica de la palabra para la tabla
     * @return Representación para humanos de la palabra
     */
    string palabraString (int codigoPalabra) {
        string strPalaba = "";
        for(int i=0; i<LETRAS; ++i) {
            int codigoLetra = codigoPalabra & 0b11;
            codigoPalabra >>= 2;
            strPalaba += letras_nombre[codigoLetra];
        }
        return strPalaba;
    }

    /**
     * Muestra la frecuencia de aparición de todas las palabras que se han contado
     */
    void print () {
        for(int comb=0; comb<COMB_PALABRAS; comb++) {
            std::cout << this->palabraString(comb) << '\t' << apariciones [comb] << "\n";
        }
        std::cout << endl;
    }

    /**
     * Llamar cuando encuentra un caracter no reconicido
     */
    void resetPalabra() {
        letras = 0;
    }

    /**
     * Reinicia todos los atributos de contador
     */
    void reset() {
        letras = 0;
        for(int i=0; i<COMB_PALABRAS; ++i) {
            apariciones[i] = 0;
        }
        palabras = 0;
    }
    
    /**
     * Devuelve el número de núcleocitos que se han contado
     * @return  número de núcleocitos que se han contado
     */
    int getCaracteres() { return caracteres; }


    /**
     * Devuelve el número de palabras que se han contado en el génoma de longitud k
     * @return número de palabras de longitud k 
     */
    int getPalabras () {return palabras;}

    /**
     * Devuelve las frecuencias relativas de todos los k-meros a partir de las frecuencias absolutas 
     * @return  array de frecuencias relativas
     */
    double* frecuenciasRelativas () {
        double* fr = new double [COMB_PALABRAS];   // array de frecuencias relativas
        double INV_PALABRAS = 1.0/palabras;        // inversa del número de palabras contadas

        for(int i=0; i<COMB_PALABRAS; i++)
            fr[i] = apariciones[i] * INV_PALABRAS;
        
        return fr;
    }

    /**
     * Devuelve la distancia entre 2 genemas
     * @param  fr   Array de frecuencias realitvas de las palabras calculadas que el mismo k que this
     * @return  distancia
     */
    double distancia (double* fr) {
        double* this_fr = this->frecuenciasRelativas();

        double dist = 0.0;
        for(int i=0; i<COMB_PALABRAS; ++i) {
            double diff = fr[i]-this_fr[i];
            dist += diff*diff;
        }

        delete[] this_fr;
        return sqrt(dist);
    }
};

/**
 * Obtner el cuarto campo que es el nombre del genoma, el NC
 * @param linea línea de información completa
 * @return campo NC del genoma
 */
string campoNC (const string& linea) {
    const int SIZE = linea.size();
    int ini = 0;
    int aparaciones = 0;

    while (ini<SIZE && aparaciones<3) {
        if(linea[ini] == '|')
            ++aparaciones;
        ++ini;   
    }

    int fin = ini;
    while (fin<SIZE) {
        if(linea[fin] == '|')
            break;
        ++fin;    
    }

    if(fin >= SIZE)
        fin = SIZE-1;

    return linea.substr(ini, fin-ini);
}

/**
 * Muestra Array de JS, con el cálculo del valor GS desde k_min hasta su máximo o k_max
 * @param argv[1]   Nombre del fichero FASTA de genoma único
 * @param argv[2]   Nombre del fichero MULTIFASTA
 * @param argv[3]   k, tamaño de las palabras
 * @return 0 si finaliza correctamente
 */
int main(int argc, char* argv[]) {
    string nombreFASTA;
    string nombreMULTI;
    int k;
    
    // INICIALIZAR PARÁMETROS
    if(argc < 4) {  // Llamar sin argumentos -> asignar unospor defecto
        nombreFASTA = "Clostridioides00000.fa";
        nombreMULTI = "393phages.fasta";
        k = 5;
    } else {        // Llamada con argumentos
        nombreFASTA = argv[1];
        nombreMULTI = argv[2];
        k = atoi(argv[3]);
    }

    // CONTAR K-MERS DEL FICHERO FASTA Y OBTENER SU FRECUENCIAS RELATIVAS
    double* frecuenciasRelativas;
    {
        Contador contadorFASTA(k);
        ifstream archivo(nombreFASTA);

        if(!archivo.is_open()) {
            std::cout << "No se pudo leer el archivo" << endl;
            return -1;
        }

        string linea;
        getline(archivo, linea); // la primera linea siempre es un comentario que se elimina
        while (getline(archivo, linea)) {

            if (linea[0] == '>') {continue;} // leer comentario

            for(auto c: linea) {
                switch (c) {
                    case 'A':
                        contadorFASTA.contar(Letra::A);
                        break;

                    case 'C':
                        contadorFASTA.contar(Letra::C);
                        break;

                    case 'G':
                        contadorFASTA.contar(Letra::G);
                        break;

                    case 'T':
                        contadorFASTA.contar(Letra::T);
                        break;

                    default:
                        contadorFASTA.resetPalabra();
                        break;
                }
            }
        }
        frecuenciasRelativas = contadorFASTA.frecuenciasRelativas();
    }

    cout << "[";
    Contador contadorMULTI(k); 
    string nc;
    {
        ifstream archivo(nombreMULTI);
        string linea;

        // La 1a línea siempre es la descripción del 1er genoma
        getline(archivo, linea);
        nc = campoNC(linea); 

        while (getline(archivo, linea)) {

            // Se encuentra un nuevo genoma
            if (linea[0] == '>') {
                cout << "{x:" << contadorMULTI.distancia(frecuenciasRelativas) << ", "
                     <<  "y:" << contadorMULTI.getPalabras() << ", "
                     <<  "name:'" <<nc<< "'}, \n";

                nc = campoNC(linea);
                contadorMULTI.reset();
            } 

            for(auto c: linea) {
                switch (c) {
                    case 'A':
                        contadorMULTI.contar(Letra::A);
                        break;

                    case 'C':
                        contadorMULTI.contar(Letra::C);
                        break;

                    case 'G':
                        contadorMULTI.contar(Letra::G);
                        break;

                    case 'T':
                        contadorMULTI.contar(Letra::T);
                        break;

                    default:
                        contadorMULTI.resetPalabra();
                        break;
                }
            }
        }
    }

    cout << "{x:"<<contadorMULTI.distancia(frecuenciasRelativas)<<", "
         <<  "y:"<<contadorMULTI.getPalabras()<<", "
         <<  "name:'"<< nc <<"'}]";

    delete [] frecuenciasRelativas;

    return 0;
}
