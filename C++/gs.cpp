/**
 * Este programa se va a utilizar para contar la frecuencia de aparición de las palabras de 3 letras
 */
#include <string>
#include <iostream>
#include <fstream>
#include <cmath>
#include <list>
#include <cstdlib>
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
     * No borrar  
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

        if(letras >= LETRAS) // Si esta es la kª letra
            apariciones [palabra] ++;
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
            cout << this->palabraString(comb) << '\t' << apariciones [comb] << "\n";
        }
        cout << endl;
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
    }
    
    /**
     * Devuelve el número de núcleocitos que se han contado
     * @return  número de núcleocitos que se han contado
     */
    int getCaracteres() { return caracteres; }

    /**
     * Ratio resultante de la comparación de la secuencia analizada
     * con una secuencia aleatoria.
     * Según el código
     * @param contar0kmers  Determina si hay que con los k-mers que tienen 0 de frecuencia
     * @return ratio en tanto por 1
     */
    double GS(const bool contar0kmers) {
        const double VALOR_ESPERADO = caracteres / double (COMB_PALABRAS);
        //cout << "VALOR ESPERADO = "<< VALOR_ESPERADO << endl;

        double suma = 0.0;
        for(int i=0; i<COMB_PALABRAS; ++i) {
            if(contar0kmers || apariciones[i]>0)
                suma += abs(apariciones[i] - VALOR_ESPERADO);
        }

        double media = suma/COMB_PALABRAS;
        return media/VALOR_ESPERADO;
    }
};

/**
 * Muestra Array de JS, con el cálculo del valor GS desde k_min hasta su máximo o k_max
 * @param argv[1]   Nombre del fichero
 * @param argv[2]   k_min
 * @param argv[3]   k_max
 * @param argv[4]   contar0kmers
 * @return 0 si finaliza correctamente
 */
int main(int argc, char* argv[]) {

    if(argc < 4) {
        cout << "Faltan párametros" << endl;
        return 1;
    }

    string nombreFichero = argv[1];
    int k_min = atoi(argv[2]);
    int k_max = atoi(argv[3]);
    bool contar0kmers = string(argv[4])=="true";

    //cout << "\ncontar0kmers = " << (contar0kmers? "true":"false") << endl;


    string resultado = "";
    double gs = 0.0;         // GS del valor de k actual
    double gs_prev = -1.0;   // GS del valor de k previo, incializado a un valor mínimo

    for(int k=k_min; k<=k_max; k++) {
        ifstream archivo(nombreFichero);

        if(!archivo.is_open()) {
            cout << "No se pudo leer el archivo" << endl;
            return -1;
        }

        Contador contador(k);
        string linea;

        getline(archivo, linea); // la primera linea siempre es un comentario que se elimina

        while (getline(archivo, linea)) {

            if (linea[0] == '>') {continue;} // leer comentario

            for(auto c: linea) {
                switch (c) {
                    case 'A':
                        contador.contar(Letra::A);
                        break;

                    case 'C':
                        contador.contar(Letra::C);
                        break;

                    case 'G':
                        contador.contar(Letra::G);
                        break;

                    case 'T':
                        contador.contar(Letra::T);
                        break;

                    default:
                        contador.resetPalabra();
                        break;
                }
            }
        }

        gs = contador.GS(contar0kmers);
        //cout << gs << endl;
        resultado += to_string(gs) + ',';

        if(gs_prev > gs) {break;}
        gs_prev = gs;

        

    }

    if(!resultado.empty())
        resultado.erase(resultado.size() - 1);
    
    cout << "[" << resultado << "]";
   

    return 0;
}