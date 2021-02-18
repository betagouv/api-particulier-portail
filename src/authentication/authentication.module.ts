import { HttpModule, Module } from '@nestjs/common';
import { ConfigModule } from '@nestjs/config';
import { authApiClientProvider } from 'src/authentication/auth-api-client.provider';
import { AuthenticationController } from 'src/authentication/authentication.controller';
import { IsAuthenticatedGuard } from 'src/authentication/is-authenticated.guard';

@Module({
  imports: [ConfigModule.forRoot(), HttpModule],
  providers: [authApiClientProvider, IsAuthenticatedGuard],
  controllers: [AuthenticationController],
  exports: [IsAuthenticatedGuard],
})
export class AuthenticationModule {}
