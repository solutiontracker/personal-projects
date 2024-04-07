import React, { ReactElement, FC, useEffect, useRef } from 'react';
import '@/src/sass/app.scss';
import RouterOutlet from '@/src/router/RouterOutlet'
import '@/node_modules/bootstrap/dist/js/bootstrap';
import { postMessage as postMessageScript } from "@/src/app/helpers";

type Props = Record<string, never>;

const App: FC<Props> = (): ReactElement => {
  return (
    <div id="App">
      <RouterOutlet />
    </div>
  );
};

export default App;